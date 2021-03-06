jQuery(document).ready(function ($) {
    var folderSelector = $("select.folders-list");
    var path = $(".path");
    var clearPath = $(".clear-path");

    folderSelector.change(function () {
        var oldPath = path.text();
        var newPath = null;
        if (oldPath === "") {
            newPath = this.value;
        } else {
            newPath = oldPath + this.value;
        }
        get_available_directorie(newPath);
    });

    $("#scan-directory").submit(function (event) {
        event.preventDefault();
        var scanPath = path.text();
        var selectedFiles = $('input[name="files-to-upload[]"]:checked').serializeArray();
        var selectionDate = $("#date-of-selectin").val();
        var dataArr = {
            action: 'save_items_as_downloads',
            path: scanPath,
            selected_files: selectedFiles,
            selection_date: selectionDate
        };
        $.ajax({
            type: "POST",
            url: ajaxurl,
            data: dataArr,
            success: function (response) {
                alert(response);
                location.reload();
            }
        });

    });

    clearPath.click(function () {
        if (path.text() !== eddeInfo.basePath) {
            var tempPath = path.text().substring(0, path.text().length - 1);
            var position = tempPath.lastIndexOf(eddeInfo.directorySeparator);
            var prevPath = tempPath.substring(0, position);
            get_available_directorie(prevPath);
        } else {
            clearPath.hide();
        }
    });

    function get_available_directorie(newPath) {
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
                    folderSelector.show();
                    folderSelector.append(responseObj.folders);
                } else {
                    folderSelector.hide();
                }
                if (responseObj.files !== "") {
                    $(".available-files").show();
                    $(".available-files").html(responseObj.files);
                } else {
                    $(".available-files").hide();
                }
                path.html(responseObj.path);
                clearBtnUpdateVisibility();
            }
        });

    }

    function clearBtnUpdateVisibility() {
        if (path.text() !== eddeInfo.basePath) {
            clearPath.show();
        } else {
            clearPath.hide();
        }
    }
});

