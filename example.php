<?php

/**
 * Copyright 2023 Jan stanray watt

 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at

 *  http://www.apache.org/licenses/LICENSE-2.0

 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

declare(strict_types=1);

require_once __DIR__.'/vendor/autoload.php';

use JSW\Danraku\DanrakuExtension;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;

$config = [
    'jisage' => [
        'ignore_alphabet' => false, // trueにすると、行頭が英数字だった場合には字下げをしなくなる
        'ignore_dash' => true,      // trueにすると、全角ダッシュ（―）、ハイフンで字下げをしなくなる
    ],
    'yakumono' => [
        'spacing_yakumono' => true, // trueにすると、「？」と「！」の前に全角スペースを空けるようになる（閉じ括弧の直前を除く）
        'byte_sensitive' => true,   // trueにすると、全角「？」「！」の場合は全角スペースを、半角「!」「?」の場合は半角スペースを挿入するようになる
    ],
];

$environment = new Environment($config);

$environment->addExtension(new CommonMarkCoreExtension())
            ->addExtension(new DanrakuExtension());

$converter = new MarkdownConverter($environment);

$markdown = 'この拡張機能は実によい・・・まさに革命的だ';

// <p>　この拡張機能は実によい・・・まさに革命的だ</p>
echo $converter->convert($markdown);
