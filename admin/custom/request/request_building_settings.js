$(document).ready(function () {
    $('#x_issue_normal[]_0').click(function (){
        $('x_issue_serious[]_0').prop('checked',false);
        $('x_issue_urgent[]_0').prop('checked',false);
    });

    $('#x_issue_normal[]_1').click(function (){
        $('x_issue_serious[]_1').prop('checked',false);
        $('x_issue_urgent[]_1').prop('checked',false);
    });
});


alert('xxxxx');