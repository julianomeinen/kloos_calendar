if (window.rcmail) {
    rcmail.addEventListener('beforeswitch-task', function(prop) {
        // catch clicks to calendar task button
        console.log('prop',prop);
        if (prop == 'calendar') {
            if (rcmail.task == 'calendar'){  // we're already there
                return false;
            }
            var url = rcmail.url('calendar.show_calendar');
            rcmail.redirect(url, false);
            return true;
        }
    });
}

console.log('Kloos Calendar is active');