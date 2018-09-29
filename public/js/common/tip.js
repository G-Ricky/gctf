(function(wu) {
    function Tip() {
        this._tool = wu;
    }

    Tip.prototype.success = function(message) {
        this._tool.showToast({
            title: message,
            icon: 'icon-success'
        });
    };

    Tip.prototype.error = function(message) {
        this._tool.showToast({
            title: message,
            icon: 'icon-error'
        });
    };

    Tip.prototype.warning = function(message) {
        this._tool.showToast({
            title: message,
            icon: 'icon-error'
        });
    };

    Tip.prototype.info = function(message) {
        this._tool.showToast({
            title: message,
            icon: 'icon-info'
        });
    };

    Tip.prototype.loading = function(message) {
        this._tool.showLoading(message);
    };

    Tip.prototype.loaded = function() {
        this._tool.hideToast();
    };

    if(typeof define === "function" && define.amd) {
        define(["wu"], function() {
            return Tip;
        });
    }else{
        window.tip = new Tip();
    }

})(wu);