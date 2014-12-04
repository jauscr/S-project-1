window.require.define({"player/index": function(exports, require, module) {
(function() {
  var Controller, Player,
    __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; },
    __hasProp = {}.hasOwnProperty,
    __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

  Controller = require('spine/controller');

  Player = (function(_super) {
    __extends(Player, _super);

    Player.prototype.className = 'player';

    Player.prototype.template = 'player/player';

    Player.prototype.events = {
      'click .player_control': 'toggle',
      'click .actions': 'toggleClass',
      'click .actions_list': 'toggleClass',
      'click .player_wave svg path': 'setProgress',
      'mousemove .player_wave path': 'onhover',
      'mouseout  .player_wave': 'onmouseout',
      'mousedown': 'mousedown'
    };

    Player.prototype.elements = {
      '.progress_playing': 'wave_progress',
      '.player_wave': 'wave',
      '.progress_hover': 'wave_hover',
      '.progress_time': 'progress_time',
      '.actions_list': 'actions_list'
    };

    function Player(options, config) {
      this.reset = __bind(this.reset, this);
      this.onfinish = __bind(this.onfinish, this);
      this.keydown = __bind(this.keydown, this);
      this.setTime = __bind(this.setTime, this);
      this.whileplaying = __bind(this.whileplaying, this);
      this.toggleClass = __bind(this.toggleClass, this);
      this.toggle = __bind(this.toggle, this);
      this.playState = __bind(this.playState, this);
      this.ready = __bind(this.ready, this);
      var settings,
        _this = this;
      Player.__super__.constructor.apply(this, arguments);
      settings = {
        url: config.swf,
        flashVersion: 9,
        debugMode: false,
        preferFlash: false,
        useWaveformData: true,
        usePeakData: true
      };
      soundManager.setup(settings).onready(this.ready);
      this.actions = {
        whileplaying: this.whileplaying,
        whileloading: this.whileloading,
        onload: this.onload,
        onfinish: this.onfinish,
        onplay: this.playState,
        onpause: this.playState,
        onresume: this.playState
      };
      $(document).bind('keydown', this.keydown);
      this.waveform = $('<svg>');
      this.waveformOverlay = $('<svg>');
      this._fetchWaveform(this.waveformUrl).done(function() {
        _this.wave.append(_this.waveform);
        _this.wave_progress.prepend(_this.waveform.clone());
        return _this.wave_hover.append(_this.waveform.clone());
      });
    }

    Player.prototype.mousedown = function(e) {
      return e.originalEvent.preventDefault();
    };

    Player.prototype.ready = function() {
      this.audio = soundManager.createSound($.extend({
        id: 'mySong',
        url: this.song_path,
        volume: 100,
        autoPlay: false,
        useWaveformData: true
      }, this.actions));
      soundManager.load('mySong');
      return this.progress_time.text("00:00");
    };

    Player.prototype.playState = function(play) {
      this.$('.player_control').toggleClass('playing', play != null ? play : !this.audio.paused);
      return this;
    };

    Player.prototype.toggle = function() {
      return soundManager.togglePause('mySong');
    };

    Player.prototype.toggleClass = function() {
      return this.actions_list.fadeToggle(200);
    };

    Player.prototype.whileplaying = function(progress) {
      var wid;
      wid = progress != null ? progress : Math.ceil((this.audio.position * this.wave.width()) / (this.audio.loaded ? this.audio.duration : this.audio.durationEstimate));
      this.wave_progress.css('width', wid + "px");
      return this.setTime(false);
    };

    Player.prototype.setTime = function(finish) {
      var d, hours, min, pos, sec, time;
      if (finish == null) {
        finish = true;
      }
      pos = Math.floor(this.audio.position);
      d = new Date(pos);
      hours = pos.toString().length < 8 ? 0 : d.getHours();
      min = d.getMinutes().toString().length < 2 ? "0" + d.getMinutes() : d.getMinutes();
      sec = d.getSeconds().toString().length < 2 ? "0" + d.getSeconds() : d.getSeconds();
      time = (hours > 0 ? hours + ":" : "") + min + ":" + sec;
      return this.progress_time.css('left', finish === true ? "10px" : this.wave_progress.width() < (this.progress_time.width() + 10) ? this.wave_progress.width() + 10 + "px" : this.wave_progress.width() - this.progress_time.width() - 10 + "px").text(time);
    };

    Player.prototype.keydown = function(e) {
      var action;
      action = {
        32: this.toggle
      }[e.keyCode || e.which];
      if ((action != null) && !$(e.target).is('input,textarea,select')) {
        e.preventDefault();
        return action.call(this);
      }
    };

    Player.prototype.onhover = function(e) {
      var localX, localY;
      localX = e.pageX - this.el.offset().left;
      localY = e.pageY - this.el.offset().top;
      if (localX <= this.wave_progress.width()) {
        this.wave_hover.css('z-index', 4).children('svg').css('fill', "rgba(255, 255, 255, 0)");
      }
      return this.wave_hover.css({
        width: localX + "px",
        opacity: 1
      });
    };

    Player.prototype.onmouseout = function(e) {
      return this.wave_hover.css({
        width: 0,
        opacity: 0,
        zIndex: 2
      }).children('svg').css('fill', '#4b4b4b');
    };

    Player.prototype.setProgress = function(e) {
      var localX, localY;
      if (this.audio.playState === 0) {
        this.toggle();
      }
      localX = e.pageX - this.el.offset().left;
      localY = e.pageY - this.el.offset().top;
      this.audio.setPosition(this.audio.loaded ? (localX * this.audio.duration) / this.wave.width() : this.audio.durationEstimate);
      return this.setTime(false);
    };

    Player.prototype._fetchWaveform = function(url) {
      var _this = this;
      return $.ajax({
        url: url
      }).done(function(data) {
        return _this.waveform = $(data).find('svg');
      });
    };

    Player.prototype.onfinish = function() {
      return this.reset();
    };

    Player.prototype.reset = function() {
      this.wave_progress.css('width', "1px");
      this.playState(false);
      this.audio.position = 0;
      this.progress_time.css('left', '10px');
      return this.setTime(true);
    };

    return Player;

  })(Controller);

  module.exports = Player;

}).call(this);
}});


window.require.define({"player/player": function(exports, require, module) {
module.exports=function anonymous(locals) {
var buf = [];
var locals_ = (locals || {}),song_title = locals_.song_title,song_artist = locals_.song_artist;buf.push("<div class=\"player_control\"></div><div class=\"player_details\"><div class=\"song_name\">" + (jade.escape((jade.interp = song_title) == null ? '' : jade.interp)) + "</div><div class=\"song_artist\">" + (jade.escape((jade.interp = song_artist) == null ? '' : jade.interp)) + "</div></div><div class=\"song_rate\"></div><div class=\"actions\"> <span>Actions</span></div><ul class=\"actions_list\"><li class=\"actions_pick\">Pick as winner</li><li class=\"actions_remove\">Remove</li></ul><div class=\"player_wave\"><div class=\"progress_hover\"></div><div class=\"progress_playing\"><a class=\"progress_time\"></a></div></div>");;return buf.join("");
}}});