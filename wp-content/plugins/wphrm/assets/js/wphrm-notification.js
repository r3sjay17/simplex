/*
 * WP-HRM
 * Copyright 2014-2016 IndigoThemes
 */
jQuery(document).ready(function () {
    window.setTimeout(function () {
        wphrm_notification_data = {
            'action': 'WPHRMNotificationInfo',
        }
        jQuery.post(ajaxurl, wphrm_notification_data, function (response) {
            console.log('Check notifications: ', response);
            if (response != 'null') {
                var data = JSON.parse(response);
                jQuery.each(data, function (key, value) {
                    var id = value.id;
                    var title = value.title;
                    var desc = value.desc;
                    var logo = value.logo;
                    ListenerNotification(title, desc, id, logo);
                });
            }
        });
    }, 3000);
});

function ListenerNotification(title, desc, id, logo) {
    notifyBrowser(title, desc, id, logo);
}
function notifyBrowser(title, desc, id, logo) {
    if (!Notification) {
        console.log('Desktop notifications not available in your browser..');
        return;
    }
    if (Notification.permission !== "granted") {
        Notification.requestPermission();
    } else {
        var notification = new Notification(title, {
            icon: logo, body: desc,
        });
        // Remove the notification from Notification Center when clicked.
        notification.onclick = function () {
            var ajax_url = ajaxurl;
            wphrm_notification_data = {
                'action': 'WPHRMNotificationStatusChangeInfo',
                'notification_id': id
            }

            jQuery.post(ajax_url, wphrm_notification_data, function (data) {


            });
        };
        notification.onclose = function () {
            var ajax_url = ajaxurl;
            wphrm_notification_data = {
                'action': 'WPHRMNotificationStatusChangeInfo',
                'notification_id': id
            }
            jQuery.post(ajax_url, wphrm_notification_data, function (data) { });
        };
    }
}

