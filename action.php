<?php
  if (!defined('DOKU_INC')) die();

  if (!defined('DOKU_LF')) define('DOKU_LF', "\n");
  if (!defined('DOKU_TAB')) define('DOKU_TAB', "\t");
  if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');

  require_once DOKU_PLUGIN . 'action.php';

  /*
   * plugin should use this method to register its handlers
   * with the dokuwiki's event controller
   */

  class action_plugin_spreadout extends DokuWiki_Action_Plugin {
    function register(Doku_Event_Handler $controller) {
      $controller->register_hook('RENDERER_CONTENT_POSTPROCESS', 'AFTER', $this, '_spreadout_postprocess', array());
    }

    /**
     * Reprocess the text to handle typography settings.
     *
     * <p>
     * Any text sent here is checked with effectively the same code
     * that is used in <tt>/inc/Parsing/Parsermode/Quotes.php</tt>.
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
     * @param $event Object The event object passed in.
     * @param $param variant The parameters passed to register_hook
     * @public
     * @see handle()
     */
    function _spreadout_postprocess(Doku_Event $event, $param) {
      global $lang, $conf;

      $ws   =  '\s/\#~:+=&%@\-\x28\x29\]\[{}><"\'';   // whitespace
      $punc =  ';,\.?!';

      if ($conf['typography_orig'] == 2) {
        $event->data[1] = preg_replace("`(?<=^|[$ws])&#039;(?=[^$ws$punc])`", $lang['singlequoteopening'], $event->data[1]);
        $event->data[1] = preg_replace("`(?<=^|[^$ws]|[$punc])&#039;(?=$|[$ws$punc])`", $lang['singlequoteclosing'], $event->data[1]);
        $event->data[1] = preg_replace("`(?<=^|[^$ws$punc])&#039;(?=$|[^$ws$punc])`", $lang['apostrophe'], $event->data[1]);
      }

      if ($conf['typography_orig'] > 0) {
        $event->data[1] = preg_replace("`(?<=^|[$ws])&quot;(?=[^$ws$punc])`", $lang['doublequoteopening'], $event->data[1]);
        $event->data[1] = preg_replace("`&quot;`", $lang['doublequoteclosing'], $event->data[1]);
      }
    }
  }

  global $conf;
  if (!isset($conf['typography_orig']))
    $conf['typography_orig'] = $conf['typography'];
  $conf['typography'] = 0;

?>
