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

namespace JSW\Test;

use JSW\Danraku\DanrakuExtension;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \JSW\Danraku\Parser\JisageParser
 *
 * @group unit
 * @group jisage
 */
class DanrakuJisageTest extends TestCase
{
    private const DEFAULT_RULE = [
        'danraku' => [
            'ignore_alphabet' => false,
            'ignore_dash' => true,
            'spacing_yakumono' => true,
            'byte_sensitive' => true,
        ],
    ];

    /**
     * @covers ::parse
     */
    public function testDefaultJisageJapanese(): void
    {
        $environment = new Environment($this::DEFAULT_RULE);

        $environment->addExtension(new CommonMarkCoreExtension())
                    ->addExtension(new DanrakuExtension());

        $converter = new MarkdownConverter($environment);

        $expect = '<p>　この拡張機能は素晴らしい</p>'."\n";
        $actual = $converter->convert('この拡張機能は素晴らしい')->getContent();

        $this->assertSame($expect, $actual);
    }

    /**
     * @covers ::parse
     */
    public function testDefaultJisageEnglish(): void
    {
        $environment = new Environment($this::DEFAULT_RULE);

        $environment->addExtension(new CommonMarkCoreExtension())
                    ->addExtension(new DanrakuExtension());

        $converter = new MarkdownConverter($environment);

        $expect = '<p>　This Extension is awesome</p>'."\n";
        $actual = $converter->convert('This Extension is awesome')->getContent();

        $this->assertSame($expect, $actual);
    }

    /**
     * @depends testDefaultJisageEnglish
     *
     * @covers ::parse
     * @covers \JSW\Danraku\DanrakuExtension::configureSchema
     */
    public function testJisageConfigIgnoreAlphabetTrue(): void
    {
        $rules = [
            'danraku' => [
                'ignore_alphabet' => true,
            ],
        ];

        $environment = new Environment($rules);

        $environment->addExtension(new CommonMarkCoreExtension())
                    ->addExtension(new DanrakuExtension());

        $converter = new MarkdownConverter($environment);

        $expect = '<p>This Extension is awesome</p>'."\n";
        $actual = $converter->convert('This Extension is awesome')->getContent();

        $this->assertSame($expect, $actual);
    }

    /**
     * @depends testDefaultJisageEnglish
     * @depends testDefaultJisageJapanese
     *
     * @covers ::parse
     * @covers \JSW\Danraku\DanrakuExtension::configureSchema
     */
    public function testJisageConfigIgnoreDashFalse(): void
    {
        $rules = [
            'danraku' => [
                'ignore_dash' => false,
            ],
        ];

        $environment = new Environment($rules);

        $environment->addExtension(new CommonMarkCoreExtension())
                    ->addExtension(new DanrakuExtension());

        $converter = new MarkdownConverter($environment);

        $expect_1 = '<p>　―この拡張機能は素晴らしい</p>'."\n";
        $actual_1 = $converter->convert('―この拡張機能は素晴らしい')->getContent();

        $expect_2 = '<p>　―This Extension is awesome</p>'."\n";
        $actual_2 = $converter->convert('―This Extension is awesome')->getContent();

        $this->assertSame($expect_1, $actual_1, 'Failed by Japanese');
        $this->assertSame($expect_2, $actual_2, 'Failed by English');
    }

    /**
     * @depends testDefaultJisageEnglish
     * @depends testDefaultJisageJapanese
     *
     * @covers ::parse
     * @covers \JSW\Danraku\DanrakuExtension::configureSchema
     */
    public function testJisageEscape(): void
    {
        $environment = new Environment($this::DEFAULT_RULE);

        $environment->addExtension(new CommonMarkCoreExtension())
                    ->addExtension(new DanrakuExtension());

        $converter = new MarkdownConverter($environment);

        $expect_1 = '<p>この拡張機能は素晴らしい</p>'."\n";
        $actual_1 = $converter->convert('-この拡張機能は素晴らしい')->getContent();

        $expect_2 = '<p>This Extension is awesome</p>'."\n";
        $actual_2 = $converter->convert('-This Extension is awesome')->getContent();

        $this->assertSame($expect_1, $actual_1, 'Failed by Japanese');
        $this->assertSame($expect_2, $actual_2, 'Failed by English');
    }

    /**
     * @depends testJisageEscape
     *
     * @covers ::parse
     * @covers \JSW\Danraku\DanrakuExtension::configureSchema
     */
    public function testJisageEscapeEscape(): void
    {
        $environment = new Environment($this::DEFAULT_RULE);

        $environment->addExtension(new CommonMarkCoreExtension())
                    ->addExtension(new DanrakuExtension());

        $converter = new MarkdownConverter($environment);

        $expect_1 = '<p>　-この拡張機能は素晴らしい</p>'."\n";
        $actual_1 = $converter->convert('\-この拡張機能は素晴らしい')->getContent();

        $expect_2 = '<p>　-This Extension is awesome</p>'."\n";
        $actual_2 = $converter->convert('\-This Extension is awesome')->getContent();

        $this->assertSame($expect_1, $actual_1, 'Failed by Japanese');
        $this->assertSame($expect_2, $actual_2, 'Failed by English');
    }

    /**
     * @depends testDefaultJisageJapanese
     *
     * @covers ::parse
     */
    public function testIgnoreBlockHead(): void
    {
        $environment = new Environment($this::DEFAULT_RULE);

        $environment->addExtension(new CommonMarkCoreExtension())
                    ->addExtension(new DanrakuExtension());

        $converter = new MarkdownConverter($environment);

        $expect_1 = '<h1>この拡張機能は素晴らしい</h1>'."\n";
        $actual_1 = $converter->convert('# この拡張機能は素晴らしい')->getContent();

        $expect_2 = '<h2>この拡張機能は素晴らしい</h2>'."\n";
        $actual_2 = $converter->convert('## この拡張機能は素晴らしい')->getContent();

        $expect_3 = '<h3>この拡張機能は素晴らしい</h3>'."\n";
        $actual_3 = $converter->convert('### この拡張機能は素晴らしい')->getContent();

        $expect_4 = '<h4>この拡張機能は素晴らしい</h4>'."\n";
        $actual_4 = $converter->convert('#### この拡張機能は素晴らしい')->getContent();

        $expect_5 = '<h5>この拡張機能は素晴らしい</h5>'."\n";
        $actual_5 = $converter->convert('##### この拡張機能は素晴らしい')->getContent();

        $expect_6 = '<h6>この拡張機能は素晴らしい</h6>'."\n";
        $actual_6 = $converter->convert('###### この拡張機能は素晴らしい')->getContent();

        $this->assertSame($expect_1, $actual_1, 'Failed by <h1>');
        $this->assertSame($expect_2, $actual_2, 'Failed by <h2>');
        $this->assertSame($expect_3, $actual_3, 'Failed by <h3>');
        $this->assertSame($expect_4, $actual_4, 'Failed by <h4>');
        $this->assertSame($expect_5, $actual_5, 'Failed by <h5>');
        $this->assertSame($expect_6, $actual_6, 'Failed by <h6>');
    }

    /**
     * @depends testDefaultJisageJapanese
     *
     * @covers ::parse
     */
    public function testMergeInlineLink(): void
    {
        $environment = new Environment($this::DEFAULT_RULE);

        $environment->addExtension(new CommonMarkCoreExtension())
                    ->addExtension(new DanrakuExtension());

        $converter = new MarkdownConverter($environment);

        $expect_1 = '<p>　<a href="www.example.com">この拡張機能は素晴らしい</a></p>'."\n";
        $actual_1 = $converter->convert('[この拡張機能は素晴らしい](www.example.com)')->getContent();

        $this->assertSame($expect_1, $actual_1);
    }

    /**
     * @depends testDefaultJisageJapanese
     *
     * @covers ::parse
     */
    public function testIgnoreInlineImage(): void
    {
        $environment = new Environment($this::DEFAULT_RULE);

        $environment->addExtension(new CommonMarkCoreExtension())
                    ->addExtension(new DanrakuExtension());

        $converter = new MarkdownConverter($environment);

        $expect = '<p><img src="img/example.png" alt="この拡張機能は素晴らしい" /></p>'."\n";
        $actual = $converter->convert('![この拡張機能は素晴らしい](img/example.png)')->getContent();

        $this->assertSame($expect, $actual);
    }
}
