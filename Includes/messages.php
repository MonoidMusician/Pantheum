	<div class="floater-message error">
	<a>Javascript did not successfully load. Some/all functionality may not be available.</a>
	</div>
	<script type="text/javascript">
		$('.floater-message').hide();
		messageTip = (function() {
			var timer = null;
			var ignore_ = {};
			function callback(ignore) {
				if (ignore !== ignore_) $('.floater-message').hide();
				if (timer !== null)
					clearTimeout(timer);
			}
			function messageTip(msg, delay) {
				if (delay === 0 || !msg) return callback();
				if (delay === undefined) delay = 2300;
				callback(true);
				$('.floater-message').show().removeClass('error').removeClass('success');
				$('.floater-message a').html(msg);
				if (delay !== null)
					timer = setTimeout(callback, delay);
			}
			$('.floater-message').on("click", callback);
			return messageTip;
		})();
		errorTip = function(msg, delay) {
			if (delay === undefined) delay = null;
			messageTip(msg, delay);
			$('.floater-message').addClass('error');
		};
		successTip = function(msg, delay) {
			messageTip(msg, delay);
			$('.floater-message').addClass('success');
		};
	</script>
