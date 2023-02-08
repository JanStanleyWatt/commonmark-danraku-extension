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

namespace JSW\Danraku\Parser;

use League\CommonMark\Node\Block\Document;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Parser\Inline\InlineParserInterface;
use League\CommonMark\Parser\Inline\InlineParserMatch;
use League\CommonMark\Parser\InlineParserContext;
use League\Config\ConfigurationAwareInterface;
use League\Config\ConfigurationInterface;

/**
 * 段落処理のメインパーサ。処理の優先順位は90以上にすること(EscapableParserとの兼ね合いのため).
 */
class JisageParser implements InlineParserInterface, ConfigurationAwareInterface
{
    private ConfigurationInterface $config;

    public function setConfiguration(ConfigurationInterface $configuration): void
    {
        $this->config = $configuration;
    }

    public function getMatchDefinition(): InlineParserMatch
    {
        return InlineParserMatch::regex('^.');
    }

    public function parse(InlineParserContext $inline_context): bool
    {
        $ignore_alphabet = $this->config->get('danraku/ignore_alphabet');
        $ignore_dash = $this->config->get('danraku/ignore_dash');

        $cursor = $inline_context->getCursor();
        $now_char = $cursor->getCurrentCharacter();
        $container = $inline_context->getContainer();

        if ('-' === $now_char) {
            $cursor->advance();

            return true;
        }

        if ($ignore_alphabet && mb_ereg_match('[a-zA-Z0-9]', $now_char)) {
            return false;
        }

        if ($ignore_dash && mb_ereg_match('―', $now_char)) {
            return false;
        }

        if ($container instanceof Paragraph && $container->parent() instanceof Document) {
            $container->prependChild(new Text('　'));

            return false;   // trueにすると、他の記号の処理が飛ばされてしまう
        }

        // 基本的にはfalseを返す
        return false;
    }
}
