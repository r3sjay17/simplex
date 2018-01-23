<?php

class JAF_Wphrm_Extend {

    function __construct( ) {
        add_action('init', array($this, 'employee_auto_present') );
    }

    /*
    Set the employee attendance to present each day
    except those employee the applied for leave
    */
    function employee_auto_present(){

    }

}
new JAF_Wphrm_Extend();
