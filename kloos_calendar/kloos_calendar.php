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

    private $rc = null;

    public function init()
    {
        // Set the instances
        $this->rc = rcmail::get_instance();

        // Set localization
        $this->add_texts('localization', true);

        // Incude JS and CSS files
        $this->include_stylesheet("css/style.css");
        $this->include_script('js/functions.js');

        // register task
        $this->register_task('calendar');
        // register actions
        $this->register_action('index', array($this, 'show_calendar'));

        // Add the hooks
        $this->add_hook('startup', array($this, 'add_calendar_button_to_taskbar'));
    }

    public function add_calendar_button_to_taskbar($args)
    {
        $this->add_button(array(
            'command'    => 'calendar',
            'class'      => 'button_kloos_calendar',
            'innerclass' => 'inner',
            'label'      => 'kloos_calendar.button_label',
            'type'       => 'link',
        ), 'taskbar');
    }

    public function show_calendar()
    {
        $this->register_handler('plugin.calendar_html', array($this, 'get_calendar_index_html'));
        $this->rc->output->set_pagetitle($this->gettext('kloos_calendar.page_title'));
        //$rcmail->output->send('plugin');
        $this->rc->output->send('kloos_calendar.calendar');
    }

    public function get_calendar_index_html(): string
    {
        $content = '';
        $arrWeekDays = $this->get_week_days();
        
        $content .= implode(', ', $arrWeekDays);

        return $content;
    }

    private function get_week_days(bool $abreviation = false): array
    {
        $arrWeekDays = [];

        for ($i = 1; $i <= 7; $i++) {
            $arrWeekDays[] = $abreviation === true ? mb_substr($this->gettext('kloos_calendar.weekday_' . $i), 0, 3) : $this->gettext('kloos_calendar.weekday_' . $i);
        }
        return $arrWeekDays;
    }
}
