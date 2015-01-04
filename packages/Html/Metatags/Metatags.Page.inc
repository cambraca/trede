<?php

/**
 * @file Adds the generated meta tags to the page's head section.
 */

namespace Html;

use Html\Page\Element;

Page::i()->head()->append(new Element('meta'));