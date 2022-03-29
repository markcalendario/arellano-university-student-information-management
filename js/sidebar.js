$(document).ready(() => {
    
    $('.show-side-bar').click(() => {

        if (isSideBarOpen()) {
            makeSideBarClose();
        } else {
            makeSideBarOpen();
        }

        function makeSideBarClose() {
            $('.sidebar-pane')
                .css('animation', 'sideBarAnimationClosing 0.3s');
                
                setTimeout(() => {
                    $('.sidebar-pane').css('display', 'none');
                }, 200);
                
        }

        function makeSideBarOpen() {
            $('.sidebar-pane')
                .css('display', 'block')
                .css('animation', 'sideBarAnimationOpening 0.3s');
            
        }

        function isSideBarOpen() {
            
            $sideBarStatus = $('.sidebar-pane').css('display');

            if ($sideBarStatus == 'none') {
                return 0;
            } else {
                return 1;
            }

        }

    });

});