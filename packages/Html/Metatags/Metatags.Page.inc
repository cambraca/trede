<?php

/**
 * @file Adds the generated meta tags to the page's head section.
 */

namespace HTML;

use HTML\Page\Element;

Page::i()->head()->append(new Element('meta'));