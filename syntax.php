<?php
  if (!class_exists('syntax_plugin_spreadout')) {
    if (!defined('DOKU_PLUGIN')) {
      if (!defined('DOKU_INC')) {
        define('DOKU_INC', realpath(dirname(__FILE__) . '/../../') . '/');
      } // if
      define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
    } // if
    // Include parent class:
    require_once(DOKU_PLUGIN . 'syntax.php');

    /**
     * <tt>syntax_plugin_spreadout.php</tt>, a PHP class that lets the
     * traditional amongst us to have double spaces after a sentence.
     *
     * <p>
     * This automatically detects and replaces double spaces after sentences.
     * Basically, any text that ends with a period ('.'), question mark ('?')
     * or exclamation point ('!'), with an optional bracket after that
     * punctuation, followed by two or more regular spaces, will be replaced
     * with a space followed by a non-breaking space entity ("&nbsp;").  This
     * makes things "spread out" by adding double space punctuation afterwards.
     * </p><p>
     * Before you ask, yes, I am a later generation than millennial.
     * </p>
     *
     * <div class="disclaimer">
     * This program is free software; you can redistribute it and/or modify
     * it under the terms of the GNU General Public License as published by
     * the Free Software Foundation; either
     * <a href="http://www.gnu.org/licenses/gpl.html">version 3</a> of the
     * License, or (at your option) any later version.<br>
     * This software is distributed in the hope that it will be useful,
     * but WITHOUT ANY WARRANTY; without even the implied warranty of
     * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
     * See the GNU General Public License for more details.
     * </div>
     *
     * @author <a href="mailto:mpb@pobox.com">Michael Bowers</a>
     * @version v1.0
     */
    class syntax_plugin_spreadout extends DokuWiki_Syntax_Plugin {

      /**
       * Tell the parser whether the plugin accepts syntax mode
       * <tt>$aMode</tt> within its own markup.
       *
       * <p>
       * This method always returns <tt>FALSE</tt> since no other data
       * can be nested inside a non-breaking space.
       * </p>
       * @param $aMode String The requested syntaxmode.
       * @return Boolean <tt>FALSE</tt> always.
       * @public
       * @see getAllowedTypes()
       */
      function accepts($aMode) {
        return FALSE;
      } // accepts()

      /**
       * Connect lookup patterns to lexer.
       *
       * @param $aMode String The desired rendermode.
       * @public
       * @see render()
       */
      function connectTo($aMode) {
        global $lang;

//<E2><80><9C>
        $this->Lexer->addSpecialPattern('(?<=[.\?\!\:]) {2,}', $aMode, 'plugin_spreadout');
        $this->Lexer->addSpecialPattern('(?<=[.\?\!\:][\)\]\}\"\']) {2,}', $aMode, 'plugin_spreadout');
        $this->Lexer->addSpecialPattern('(?<=[.\?\!\:][\]\}\"\']) {2,}', $aMode, 'plugin_spreadout');

        // $this->Lexer->addPattern('', $aMode, 'plugin_spreadout_processing');
      } // connectTo()

      /**
       * Get an associative array with plugin info.
       *
       * <p>
       * The returned array holds the following fields:
       * <dl>
       * <dt>author</dt><dd>Author of the plugin</dd>
       * <dt>email</dt><dd>Email address to contact the author</dd>
       * <dt>date</dt><dd>Last modified date of the plugin in
       * <tt>YYYY-MM-DD</tt> format</dd>
       * <dt>name</dt><dd>Name of the plugin</dd>
       * <dt>desc</dt><dd>Short description of the plugin (Text only)</dd>
       * <dt>url</dt><dd>Website with more information on the plugin
       * (eg. syntax description)</dd>
       * </dl>
       * @return Array Information about this plugin class.
       * @public
       * @static
       */
      function getInfo() {
        return array (
          'author' =>	'Michael Bowers',
          'email' =>	'mpb@pobox.com',
          'date' =>	'2022-07-22',
          'name' =>	'Spreadout Plugin',
          'desc' =>	'A simple plugin that allows for two spaces between content sentences if the user types two spaces rather than one.',
          'url' =>	'http://www.dokuwiki.org/plugin:spreadout');
      } // getInfo()

      /**
       * Where to sort in?
       *
       * @return Integer <tt>174</tt>.
       * @public
       * @static
       */
      function getSort() {
        return 174;
      } // getSort()

      /**
       * Get the type of syntax this plugin defines.
       *
       * @return String <tt>'substition'</tt> (a mispelled 'substitution').
       * @public
       * @static
       */
      function getType() {
        return 'disabled';
      } // getType()

      /**
       * Handler to prepare matched data for the rendering process.
       *
       * <p>
       * This implementation does nothing (ignoring the passed
       * arguments) and just returns the given <tt>$aState</tt>.
       * </p>
       *
       * @param $aMatch String The text matched by the patterns.
       * @param $aState Integer The lexer state for the match.
       * @param $aPos Integer The character position of the matched text.
       * @param $aHandler Object Reference to the Doku_Handler object.
       * @return Integer The given <tt>$aState</tt> value.
       * @public
       * @see render()
       * @static
       */
      function handle($aMatch, $aState, $aPos, Doku_Handler $aHandler) {
        return $aState;		// nothing more to do here ...
      } // handle()

      /**
       * Reprocess the text to handle typography settings.
       *
       * <p>
       * Any text sent here is checked with effectively the same code
       * that is used in <tt>/inc/Parser/Parsermode/Quotes.php</tt>.
       * The reason for this is because when you are using typography
       * settings to render curly quotes then they are tokenized and
       * handled outside the level a syntax plugin can handle.  To make
       * it so that quoted sentence breaks can be handled we have to
       * turn off typography.
       * </p><p>
       * This is undesirable because I suspect most users turn on at
       * least curly double quotes the settings, so what I do is still
       * turn it off, but upon loading this plugin I grab a copy of the
       * value first and duplicate its work by processing the text to
       * transform the characters in the rendered output from the
       * default values.  It's a <em>hideous</em> hack, but it seems to
       * work (at least for English), and people who know my actual
       * work should know I have to dole out some hideous hacks to do
       * what I am usually asked to do, so...
       * </p>
       *
       * @param $text String The aggregated rendered text from the renderer object.
       * @public
       * @see handle()
       */
      function reprocessDoc($text) {
        global $lang;

      }

      /**
       * Handle the actual output creation.
       *
       * <p>
       * The method checks for the given <tt>$aMode</tt> and returns
       * <tt>FALSE</tt> when a mode isn't supported.
       * <tt>$aRenderer</tt> contains a reference to the renderer object
       * which is currently handling the rendering.
       * The contents of <tt>$aData</tt> is the return value of the
       * <tt>handle()</tt> method.
       * </p><p>
       * This implementation ignores the passed <tt>$aFormat</tt>
       * argument adding a raw UTF-8 character sequence to the
       * renderer's document.
       * </p>
       *
       * @param $aFormat String The output format to generate.
       * @param $aRenderer Object A reference to the renderer object.
       * @param $aData Integer The state value returned by <tt>handle()</tt>.
       * @return Boolean <tt>TRUE</tt> always.
       * @public
       * @see handle()
       */
      function render($aFormat, Doku_Renderer $aRenderer, $aData) {
        if (DOKU_LEXER_SPECIAL == $aData) {
          $aRenderer->doc .= '&nbsp; ';
        } // if
        return TRUE;
      } // render()
    } // class syntax_plugin_nbsp
  } // if

?>
