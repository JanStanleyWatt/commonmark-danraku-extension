# Danraku
[League/CommonMark](https://commonmark.thephpleague.com/) extension for japanese danraku style.

自動で段落の頭に全角スペースを入れてくれたり、区切り約物の直後に全角スペースを入れてくれる[League/CommonMark](https://commonmark.thephpleague.com/)拡張機能。

## Install
`$ composer require whojinn/danraku`

## Usage
```php
$environment = new Environment($config);

$environment
    ->addExtension(new CommonMarkCoreExtension())
    ->addExtension(new DanrakuExtension());

$converter = new MarkdownConverter($environment);

$markdown = 'この拡張機能は実によい・・・まさに革命的だ';

//<p>　この拡張機能は実によい・・・まさに革命的だ</p>
echo $converter->convert($markdown);
```

## config
```php
// 以下、デフォルトでの設定
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
```

## Licence
Apache License, Version 2.0  
- [英語原文](https://www.apache.org/licenses/LICENSE-2.0)