window.onload = function() {
    var doc = document
        , win = window
        , $loading = doc.getElementById('loading')
        , $results_mask = doc.getElementById('main-results_mask')
        , $url_input = doc.getElementById('url-input')
        , $url_submit = doc.getElementById('url-submit')
        , $url_example = doc.getElementById('url-example')
        , $result_name = doc.getElementById('result-name')
        , $result_btns = doc.getElementById('result-btns');

    function removeClass(el, className) {
        el.className = (' ' + el.className + ' ').replace(' ' + className + ' ', ' ').replace(/[\s\b\t\n\r\f]+/g, ' ');
    }

    function addClass(el, className) {
        if((' ' + el.className + ' ').indexOf(' ' + className + ' ') === -1) {
            el.className = (' ' + el.className + ' ').replace(/[\s\b\t\n\r\f]+/g, ' ') + className;
        }
    }

    function doNothing(event) {
        event = event || win.event;
        if(event) {
            event.cancelBubble = true;
            event.returnValue = false;
            if(event.stopPropagation) event.stopPropagation();
            if(event.preventDefault) event.preventDefault();
        }
        return false;
    }

    var Downloader = {};
    Downloader.showLoader = function() { addClass($loading, 'process') };
    Downloader.hideLoader = function() { removeClass($loading, 'process') };

    Downloader.showResult = function() { addClass($results_mask, 'open') };
    Downloader.hideResult = function() { removeClass($results_mask, 'open') };

    Downloader.getResult = function() {
        Downloader.hideResult();

        if($url_input.value != '') {
            setTimeout(Downloader.showLoader, lte_IE9 ? 0 : 250);

            setTimeout(function() {
                Ajax.post(
                    'stream_cz.php',
                    {url: $url_input.value},
                    function(data) {
                        Downloader.displayResult(Ajax.parseJSON(data));
                    },
                    function() {},
                    function() {
                        Downloader.displayResult({
                            title: '-- Nic nenalezeno --',
                            qualities: []
                        });
                    });
            }, lte_IE9 ? 0 : 650);
        }
    };
    Downloader.displayResult = function(data) {
        Downloader.hideLoader();
        setTimeout(function() {
            var key, quality, result_btns = '';

            if(data.hasOwnProperty('title') && data.title != '') {
                $result_name.value = data.title;
                for(key in data.qualities) {
                    quality = data.qualities[key];
                    result_btns += '<a href="' + quality.source + '" class="result-link btn" target="_blank">' + quality.quality + '</a>';
                }
            }

            $result_btns.innerHTML = result_btns;

            Downloader.showResult();
        }, lte_IE9 ? 0 : 350);
    };

    $url_submit.onclick = function(event) {
        doNothing(event);
        Downloader.getResult();
    };

    $url_example.onclick = function() {
        $url_input.value = this.innerHTML;
        $url_submit.focus();
    };

    Downloader.hideLoader();
    if(lte_IE8) {
        alert('Prosím, aktualizujte svůj prohlížeč.');
    }
};
