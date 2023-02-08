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
            'byte_sensitive' => true
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
        
        $this->assertSame($expect_1, $actual_1, "Failed by Japanese");
        $this->assertSame($expect_2, $actual_2, "Failed by English");
    }
    
    /**
     * @depends testDefaultJisageEnglish
     * @depends testDefaultJisageJapanese
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
        
        $this->assertSame($expect_1, $actual_1, "Failed by Japanese");
        $this->assertSame($expect_2, $actual_2, "Failed by English");
    }
    
    /**
     * @depends testJisageEscape
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
        
        $this->assertSame($expect_1, $actual_1, "Failed by Japanese");
        $this->assertSame($expect_2, $actual_2, "Failed by English");
    }
}
