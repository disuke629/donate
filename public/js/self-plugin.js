// delay loading
$(function () {
    // title
    $('[data-toggle="tooltip"]').tooltip();

    // message alert box
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "timeOut": 2000,
    };
});

// time display
function showTime() {
    let d = new Date();
    let h = addZero(d.getHours());
    let m = addZero(d.getMinutes());
    let s = addZero(d.getSeconds());
    $('#time_now').text(h + ':' + m + ':' + s);

    setTimeout('showTime()', 1000);
}

// add zero
function addZero(i) {
    if (i < 10) {
        i = "0" + i;
    }

    return i;
}

showTime();

var ckeditorConfig = {
    filebrowserImageBrowseUrl: '/admin/filemanager?type=Images',
    filebrowserImageUploadUrl: '/admin/filemanager/upload?type=Images',
    filebrowserBrowseUrl: '/admin/filemanager?type=Files',
    filebrowserUploadUrl: '/admin/filemanager/upload?type=Files',
    height: '500px'
};

var countyMap = [
    '台北市','新北市','桃園市','台中市','台南市','嘉義市','高雄市','新竹縣','苗栗縣',
    '彰化縣','南投縣','雲林縣','嘉義縣','屏東縣','宜蘭縣','花蓮縣','台東縣',
    '澎湖縣','金門縣','連江縣','基隆市','新竹市','新竹縣'
];
