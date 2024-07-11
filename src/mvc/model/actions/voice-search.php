<?php
/**
 * What is my purpose?
 *
 **/

use bbn\X;
use bbn\Str;
use bbn\File\System;
/** @var $model \bbn\Mvc\Model */
if (!$model->db->count('INFORMATION_SCHEMA.ROUTINES', ['ROUTINE_NAME' => 'LEVENSHTEIN', 'ROUTINE_SCHEMA' => $model->db->getCurrent()])) {
  $model->db->query(<<<EOD
DELIMITER //

CREATE FUNCTION LEVENSHTEIN(s1 VARCHAR(255), s2 VARCHAR(255)) RETURNS INT
BEGIN
  DECLARE s1_len, s2_len, i, j, c, c_temp, cost INT;
  DECLARE s1_char CHAR;
  DECLARE cv0, cv1 VARBINARY(256);

  SET s1_len = CHAR_LENGTH(s1);
  SET s2_len = CHAR_LENGTH(s2);
  SET cv1 = 0x00;

  IF s1_len = 0 THEN
    RETURN s2_len;
  END IF;
  IF s2_len = 0 THEN
    RETURN s1_len;
  END IF;

  SET j = 1;
  WHILE j <= s2_len DO
    SET cv1 = CONCAT(cv1, UNHEX(HEX(j)));
    SET j = j + 1;
  END WHILE;

  SET i = 1;
  WHILE i <= s1_len DO
    SET s1_char = SUBSTRING(s1, i, 1);
    SET c = i;
    SET cv0 = UNHEX(HEX(i));

    SET j = 1;
    WHILE j <= s2_len DO
      SET c = c + 1;
      SET cost = IF(s1_char = SUBSTRING(s2, j, 1), 0, 1);
      SET c_temp = CONV(HEX(SUBSTRING(cv1, j, 1)), 16, 10) + cost;
      IF c > c_temp THEN
        SET c = c_temp;
      END IF;
      SET c_temp = CONV(HEX(SUBSTRING(cv1, j+1, 1)), 16, 10) + 1;
      IF c > c_temp THEN
        SET c = c_temp;
      END IF;
      SET cv0 = CONCAT(cv0, UNHEX(HEX(c)));
      SET j = j + 1;
    END WHILE;

    SET cv1 = cv0;
    SET i = i + 1;
  END WHILE;

  RETURN c;
END//

DELIMITER ;
EOD
  );
}

if (defined("BBN_VOCAL_SEARCH") && $model->hasData('files', true)) {
  $file = $model->data['files']['audio'];
  $tmp_path = dirname(BBN_APP_PATH) . '/test-lukas/tmp/' . basename($file['name']);
  $fs = new System();
  //X::ddump($tmp_path, $file, $model->data);
  if (rename($file['tmp_name'], $tmp_path)) {
    $file = null;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://" . BBN_VOCAL_SEARCH . ":9000/asr?encode=true&task=transcribe&language=fr&vad_filter=false&word_timestamps=false&output=json");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);  
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['accept: application/json', 'Content-Type: multipart/form-data']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, [
      'audio_file' => new CURLFile($tmp_path, 'audio/x-wav', basename($file['name']))
    ]);
  
    $response = curl_exec($ch);
if (curl_errno($ch)) {
    $error_msg = curl_error($ch);
  X::ddump($error_msg);
}    
    curl_close($ch);
 /*
    $response = X::curl("http://localhost:9000/asr?encode=true&task=transcribe&language=fr&vad_filter=false&word_timestamps=false&output=json", [
      'audio_file' => new CURLFile($tmp_path, 'audio/x-wav', basename($file['name']['audio']))
    ]); */
    
    if ($response) {
      $response_data = json_decode($response, true);
    
    if (isset($response_data['text'])) {
      $transcription = $response_data['text'];
    } else {
      return ['success' => false, 'message' => 'Transcription key not found in JSON'];
    }
   
    if ($transcription) {
      // Envoi de la transcription au LLM
      $curl = curl_init();
      curl_setopt($curl, CURLOPT_URL, "http://192.168.1.115:1234/v1/chat/completions");
      curl_setopt($curl, CURLOPT_POST, 1);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
      curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode([
        "model" => "TheBloke/Mistral-7B-Instruct-v0.2-GGUF",
        "messages" => [
          ["role" => "user", "content" => $transcription]
        ],
        "temperature" => 0.7,
        "max_tokens" => -1
      ]));

      $response = curl_exec($curl);
      curl_close($curl);

      if ($response) {
        // Decode the LLM response
        $response_data = json_decode($response, true);

        // Navigate to the desired part of the response
        if (isset($response_data['choices'][0]['message']['content'])) {
          $content = $response_data['choices'][0]['message']['content'];

          // Decode this specific JSON part
          $content_data = json_decode($content, true);

          if ($content_data) {
            
            if (isset($content_data['id'])) {
              $id = $content_data['id'];
              $spec_page = $content_data['spec_page'];
              $model->db->selectOne('apst_adherents', 'id', ['easy_id' => $id]);
              // Generate the link
              echo json_encode(['success' => true, 'link' => "adherent/fiche/$id/$spec_page"]);
              exit;
              
            } elseif (isset($content_data['name'])) {
              $name = $content_data['name'];
              $fields = ['id', 'easy_id', 'nom', 'statut', 'statut_prospect',  'numero_rcs'];
              $exactRow = $model->db->rselectAll('apst_adherents', $fields, ['nom' => $name]);
              if (!$exactRow) {
                $lev = sprintf("LEVENSHTEIN(nom, '%s')", Str::escapeSquotes($name));
                $fields['lev'] = $lev;
                $rows = $model->db->rselectAll('apst_adherents', $fields, [
  [
    'field' => 'lev',
    'operator' => '<=',
    'value' => 3
  ], [
    'lev' => 'ASC'
  ]
]);
                X::log($model->db->last());
              }
              else {
                $rows = [$exactRow];
              }

              if (count($rows) === 1) {
                $row = $rows[0];
                //X::ddump($row[0]["easy_id"]);
                echo json_encode(['success' => true, 'link' => 'adherent/fiche/' . $row[0]["easy_id"] . '/' . $content_data['spec_page']]);
                exit;
              } else {
                echo json_encode(['success' => true, 'rows' => $rows]);
                exit;
              } 
              //$rows = $model->db->rselectAll('apst_adherents', ['easy_id', 'statut', 'statut_prospect', 'id', 'nom'], ['nom' => '%' . $name . '%']);
            }
            return $content_data;
            } else {
              return ['success' => false, 'message' => 'Failed to decode content JSON'];
            }
          } else {
            return ['success' => false, 'message' => 'Unexpected response format'];
          }
        } else {
          return ['success' => false, 'message' => 'Failed to get response from LLM'];
        }
      } else {
        return ['success' => false, 'message' => 'Failed to transcribe audio'];
      }
    } else {
      return ['success' => false, 'message' => 'No response from the ASR server'];
    }
  } else {
  return ['success' => false, 'message' => 'Failed to rename uploaded file'];
  }
} else {
  return ['success' => false, 'message' => 'No file data received'];
}