    function downloadFile(urlToSend) {
        $(".loader").fadeIn("slow");
        // alert(urlToSend);
        var req = new XMLHttpRequest();
        req.open("GET", urlToSend, true);
        req.responseType = "blob";
        req.onload = function (event) {
            var blob = req.response;
            var filenameinrequest = req.getResponseHeader("Content-Disposition");
            var splitString = filenameinrequest.split(";");
            var fileName = splitString[1].slice(11,splitString[1].length - 1);
            var link=document.createElement('a');
            link.href=window.URL.createObjectURL(blob);
            link.download=fileName;
            $(".loader").fadeOut("slow");
            link.click();
        };
        req.send();
    }
