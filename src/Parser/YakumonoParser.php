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

use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Parser\Inline\InlineParserInterface;
use League\CommonMark\Parser\Inline\InlineParserMatch;
use League\CommonMark\Parser\InlineParserContext;
use League\Config\ConfigurationAwareInterface;
use League\Config\ConfigurationInterface;

class YakumonoParser implements InlineParserInterface, ConfigurationAwareInterface
{
    private $config;

    public function setConfiguration(ConfigurationInterface $configuration): void
    {
        $this->config = $configuration;
    }

    public function getMatchDefinition(): InlineParserMatch
    {
        return InlineParserMatch::oneOf('?', '!', '？', '！');
    }

    public function parse(InlineParserContext $inline_context): bool
    {
        // 設定でオフになっていたらその時点でfalse
        if (!$this->config->get('yakumono/spacing_yakumono')) {
            return false;
        }

        $cursor = $inline_context->getCursor();
        $now_char = $cursor->getCurrentCharacter();
        $next_char = $cursor->peek();
        $is_null = (null === $now_char || null === $next_char);
        $is_bang = ('!' === $now_char && '[' === $next_char);
        $already = (' ' === $next_char || '　' === $next_char);

        // 飛ばす必要が無い、または飛ばせない場合はfalse
        if ($is_null || $is_bang || $already
        || mb_ereg('\p{Pe}|\n|\s|[?!？！]', $next_char)) {
            return false;
        }

        if ($this->config->get('yakumono/byte_sensitive')
        && 1 === mb_strwidth($now_char)) {
            $cursor->advance();
            $inline_context->getContainer()->appendChild(new Text($now_char.' '));

            return true;
        }

        $cursor->advance();
        $inline_context->getContainer()->appendChild(new Text($now_char.'　'));

        return true;
    }
}
