jQuery(document).ready(function ($) {
    var folderSelector = $("select.folders-list");
    var path = $(".path");

    folderSelector.change(function () {
        var oldPath = path.text();
        var newPath = null;
        if (oldPath === "") {
            newPath = this.value;
        } else {
            newPath = oldPath + this.value;
        }
        var dataArr = {
            action: "get_folders_list",
            path: newPath
        };
        $.ajax({
            type: "POST",
            url: ajaxurl,
            data: dataArr,
            success: function (response) {
                var responseObj = JSON.parse(response);
                folderSelector[0].options.length = 0;
                if (responseObj.folders !== "") {
                    folderSelector.append(responseObj.folders);
                } else {
                    folderSelector.hide();
                }
                path.html(responseObj.path);
            }
        });
    });

    $("#scan_directory").click(function (event) {
        event.preventDefault();
        var scanPath = path.text();
        dataArr ={
            action: 'save_items_as_downloads',
            path: scanPath
        }
        $.ajax({
            type: "POST",
            url: ajaxurl,
            data: dataArr,
            success: function (response) {
               alert(response);
            }
        });

    });
});