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
    private $date = null;

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
        $this->register_action('index', array($this, 'showCalendar'));

        // Add the hooks
        $this->add_hook('startup', array($this, 'addCalendarButtonToTaskbar'));

        //Set today as default day
        $this->setDate(date('Y-m-d'));
    }

    public function setYear(int $year): void
    {
        $this->setDate($year . '-' . $this->getMonth() . '-' . $this->getDay());
    }

    public function getYear(): int
    {
        return $this->date->format('Y');
    }

    public function setMonth(int $month): void
    {
        $this->setDate($this->getYear() . '-' . $month . '-' . $this->getDay());
    }

    public function getMonth(): int
    {
        return $this->date->format('m');
    }

    public function setDay(int $day): void
    {
        $this->setDate($this->getYear() . '-' . $this->getMonth() . '-' . $day);
    }

    public function getDay(): int
    {
        return $this->date->format('d');
    }

    public function setDate(string $date, $format = 'Y-m-d')
    {
        if (DateTime::createFromFormat($format, $date)->format($format) !== $date) {
            throw new Exception('Invalid date or format: date: ' . $date . ' format: ' . $format);
        }
        $newDate = DateTime::createFromFormat($format, $date);
        $this->date = $newDate;
    }

    public function getDate(): string
    {
        return $this->date->format('Y-m-d');
    }

    public function addCalendarButtonToTaskbar($args)
    {
        $this->add_button(array(
            'command'    => 'calendar',
            'class'      => 'button_kloos_calendar',
            'innerclass' => 'inner',
            'label'      => 'kloos_calendar.button_label',
            'type'       => 'link',
        ), 'taskbar');
    }

    public function showCalendar()
    {
        $this->register_handler('plugin.calendar_html', array($this, 'getCalendarHtml'));
        $this->rc->output->set_pagetitle($this->gettext('kloos_calendar.page_title'));
        //$rcmail->output->send('plugin');
        $this->rc->output->send('kloos_calendar.calendar');
    }

    public function getCalendarHtml(): string
    {
        $content = '';
        $arrWeekDays = $this->getWeekDays();

        $table = new html_table(array('cols' => count($arrWeekDays), 'class' => 'propform'));
        foreach ($arrWeekDays as $weekday) {
            $table->add('title', rcube::Q($weekday));
        }
        
        $content .= $this->getWelcomeMessage();

        $content .= html::div(['class' => 'kloos_center'], $table->show());

        return html::div(['class' => 'box formcontent'], $content);
    }

    public static function removeAccentuation(string $string): string
    {
        $arrOriginal = array('à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ü', 'ú', 'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'O', 'Ù', 'Ü', 'Ú');
        $arrReplace  = array('a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'y', 'A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U');
        return str_replace($arrOriginal, $arrReplace, $string);
    }

    private function getWeekDays(bool $abreviation = false): array
    {
        $arrWeekDays = [];

        for ($i = 1; $i <= 7; $i++) {
            $arrWeekDays[] = $abreviation === true ? kloos_calendar::removeAccentuation(mb_substr($this->gettext('kloos_calendar.weekday_' . $i), 0, 3)) : $this->gettext('kloos_calendar.weekday_' . $i);
        }
        return $arrWeekDays;
    }

    private function getWelcomeMessage(): string
    {
        $user       = $this->rc->user;
        $identity   = $user->get_identity();
        $user       = rcube::Q($identity['name']);
        $date       = DateTime::createFromFormat('d-m-Y', date('d-m-Y'));
        $weekday    = $this->getWeekDays()[$date->format('w')];
        $message    = $this->gettext('kloos_calendar.welcome_message');
        return html::div(['class' => 'kloos_welcome_message'], sprintf($message, $user, $weekday, $date->format($this->gettext('kloos_calendar.date_format'))));
    }
}
