<?php
/**
     * What is my purpose?
     *
     **/

use bbn\X;
use bbn\Str;
use Orhanerday\OpenAi\OpenAi;
/** @var $model \bbn\Mvc\Model*/

if (isset($model->data['prompt']) && isset($model->data['input'])) {

  $key = "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";
  
  $prompt = $model->data['prompt'] . "\n\n" . $model->data['input'];

  $open_ai = new OpenAi($key);

  $complete = $open_ai->completion([
    'model' => 'text-davinci-003',
    'prompt' => $prompt,
    'temperature' => 0.9,
    'max_tokens' => 2000,
    'frequency_penalty' => 0,
    'presence_penalty' => 0,
  ]);

  $complete = json_decode($complete, true);

  X::log($complete, 'chatgpt');

  if (isset($complete['error'])) {
    return [
      'success' => false,
      'error' => $complete
    ];
  }
  
  $response = preg_replace('/^[\n\r]+/', '', $complete['choices'][0]['text']);
  
  if ($model->data['id']) {
    $insert = $model->db->insert('bbn_ai_prompt_items', [
      'id_prompt' => $model->data['id'],
      'text' => $model->data['input'],
      'author' => $model->inc->user->getId(),
      'ai' => 0
    ]);
    
    $insert = $model->db->insert('bbn_ai_prompt_items', [
      'id_prompt' => $model->data['id'],
      'text' => $response,
      'author' => $model->inc->user->getId(),
      'ai' => 1
    ]);
  }

  return [
    'success' => true,
    'text' => $response
  ];
} else {

  $all = $model->db->rselectAll([
    'tables' => ['bbn_ai_prompt'],
    'fields' => [
      'bbn_ai_prompt.id',
      'output',
      'creation_date',
      'bbn_ai_prompt.id_note',
      'usage_count',
      'description',
      'content',
      'title',
      'lang'
    ],
    'join' => [
      [
        'table' => 'bbn_notes_versions',
        'on' => [
          [
            'field' => 'bbn_ai_prompt.id_note',
            'exp' => 'bbn_notes_versions.id_note'
          ]
        ]
      ],
      [
        'table' => 'bbn_notes',
        'on' => [
          [
            'field' => 'bbn_ai_prompt.id_note',
            'exp' => 'bbn_notes.id'
          ]
        ]
      ]
    ],
    'where' => [
      'latest' => 1
    ],
    'order' => [[
      'field' => 'creation_date',
      'dir' => 'DESC'
    ]]
  ]);

  return [
    "root" => $model->data['root'],
    "prompts" => $all
  ];
}

