/**
 * Basic Ajax class with callbacks
 *
 * @author    Zemistr <me@zemistr.eu>, http://www.zemistr.eu
 * @link      http://www.zemistr.eu/projects/ajax
 * @version   1.1.1
 * @copyright Zemistr 2013
 * @licence   CC-BY-SA, http://creativecommons.org/licenses/by-sa/3.0/deed.cs
 */

var Ajax = (function (w, d) {
	var default_options = {
		url: d.location,
		method: 'GET',
		data: {},
		timeout: 0,
		onloading: function () {
		},
		onsuccess: function () {
		},
		onerror: function () {
		},
		ontimeout: function () {
		}
	};

	function callback(callback, data) {
		return typeof callback == 'function' ? callback(data) : function () {
		};
	}

	function prepareOptions(options) {
		var key;
		options = options || {};

		for (key in default_options) {
			options[key] = options[key] || default_options[key];
		}

		return options;
	}

	var Query = (function () {
		var Query = {
			s: [],
			add: function (key, value) {
				this.s[this.s.length] = encodeURIComponent(key) + "=" + encodeURIComponent(value == null ? "" : value);
			},
			build: function (query) {
				var prefix;
				this.s = [];

				if ("" + query === query) {
					return query;
				}

				for (prefix in query) {
					if (query.hasOwnProperty(prefix)) {
						this.buildParams(prefix, query[prefix]);
					}
				}

				return this.s.join("&").replace(/%20/g, "+");
			},
			buildParams: function (prefix, obj) {
				var name, i;

				if (Object.prototype.toString.call(obj) === "[object Array]") {
					for (i = 0; i < obj.length; i += 1) {
						var v = obj[i];

						if (/\]\[$/.test(prefix)) {
							this.add(prefix, v);
						}
						else {
							this.buildParams(prefix + "[" + ( typeof v === "object" ? i : "" ) + "]", v);
						}
					}
				}
				else if (typeof obj === "object") {
					for (name in obj) {
						if (obj.hasOwnProperty(name)) {
							this.buildParams(prefix + "[" + name + "]", obj[name]);
						}
					}
				}
				else {
					this.add(prefix, obj);
				}
			}
		};

		return function (q) {
			return Query.build(q);
		}
	})();

	return function (options) {
		var timer, method, data, url, xhr, state, status;
		options = prepareOptions(options);

		method = options.method.toUpperCase();
		data = Query(options.data);
		url = options.url;

		if (method !== 'POST') {
			url += data != '' ? '?' + data : '';
			data = '';
		}

		xhr = new XMLHttpRequest();
		xhr.aborted = false;
		xhr.open(method, url, true);
		xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		xhr.setRequestHeader('Cache-Control', 'no-cache');
		xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
		xhr.send(data);

		if (options.timeout > 0) {
			timer = setTimeout(function () {
				xhr.abort();
				xhr.aborted = true;
				callback(options.ontimeout);
			}, options.timeout);
		}

		xhr.onreadystatechange = function () {
			if (timer) {
				clearTimeout(timer);
			}

			state = Number(xhr.readyState);
			status = Number(xhr.status);

			if (xhr.aborted) {
				return;
			}

			if (status >= 401 && status <= 404) {
				xhr.aborted = true;
				callback(options.onerror, status);
			}

			if (state >= 1 && state <= 4) {
				callback(options.onloading, state);
			}

			if (state == 4 && (status == 200 || status == 304)) {
				callback(options.onsuccess, xhr.responseText);
			}
		};

		return {
			xhr: function () {
				return xhr;
			},
			abort: function () {
				xhr.abort();
				xhr.aborted = true;
				return xhr;
			}
		};
	}
})(window, document);

Ajax.parseJSON = function (string) {
	return JSON && JSON.parse ? JSON.parse(string) : (new Function('return ' + string + ';'))();
};

Ajax.post = function (url, data, onsuccess, onloading, onerror, ontimeout) {
	return Ajax({
		url: url,
		method: 'POST',
		data: data,
		onloading: onloading,
		onsuccess: onsuccess,
		onerror: onerror,
		ontimeout: ontimeout
	});
};

Ajax.get = function (url, data, onsuccess, onloading, onerror, ontimeout) {
	return Ajax({
		url: url,
		data: data,
		onloading: onloading,
		onsuccess: onsuccess,
		onerror: onerror,
		ontimeout: ontimeout
	});
};
