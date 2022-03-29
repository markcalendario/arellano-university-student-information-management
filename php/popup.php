<?php

function showSuccessPopup($mainString) {
    ?>
    <section id="popup">
        <div class="container">
            <div class="wrapper">
                <i class="popup-icon fad fa-check-circle"></i>
                <h4 class="popup-text"><?php echo $mainString; ?></h4>
            </div>
        </div>
    </section>
    <?php
}

function showInvalidPopup($mainString, $subString) {
    ?>
    <section id="popup">
        <div class="container">
            <div class="wrapper">
                <i class="popup-icon fad fa-exclamation-circle"></i>
                <div>
                    <h4 class="popup-text"><?php echo $mainString; ?></h4>
                    <p><?php echo $subString; ?></p>
                </div>
            </div>
        </div>
    </section>
    <?php
}

function showErrorPopup($mainString, $subString) {
    ?>
    <section id="popup">
        <div class="container">
            <div class="wrapper">
                <i class="popup-icon fad fa-times-circle"></i>
                <div>
                    <h4 class="popup-text"><?php echo $mainString; ?></h4>
                    <p> <?php echo $subString; ?> </p>
                </div>
            </div>
        </div>
    </section>
    <?php
}

?>