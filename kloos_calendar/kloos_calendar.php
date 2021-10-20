<?php

/**
 * Kloos Calendar
 *
 * Adds a simple calendar.
 *
 * @version 0.1
 * @author Juliano Kloos Meinen de Souza
 * @url http://roundcube.net/plugins/kloos_calendar
 */
class kloos_calendar extends rcube_plugin
{
    
    function init()
    {
        $this->add_texts('localization', true);
        $this->include_stylesheet("css/style.css");
        $this->include_script('js/functions.js');
        $this->add_hook('startup', array($this, 'add_calendar_button_to_taskbar'));
    }

    function add_calendar_button_to_taskbar($args)
    {

        $this->add_button(array(
            'command'    => 'mail',
            'class'      => 'button_kloos_calendar',
            'innerclass' => 'inner',
            'label'      => 'kloos_calendar.button_label',
            'type'       => 'link',
        ), 'taskbar');
    }
}
