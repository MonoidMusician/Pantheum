var time$ = new Rx.Subject();

var frameHandle;
function tick(timestamp) {
	if (time$.isDisposed) {
		window.cancelAnimationFrame(frameHandle);
		return;
	}

	time$.onNext(timestamp);

	frameHandle = requestAnimationFrame(tick);
}
function wrapping$(speed, multiply=1) {
	var first;
	if (!frameHandle) tick();
	return time$.map(time => {
		if (!first) first = time;
		return ((time-first)/speed % 1)*multiply;
	}).startWith(0);
}
function cycleBetween$(speed, min=0, max=1, easing, easing2) {
	var output$ = wrapping(speed, 2);
	if (easing && easing2) output$ = output$.map(v => v < 1 ? easing2(1-v) : easing(v-1));
	if (!easing || !easing2) output$ = output$.map(v => v < 1 ? 1-v : v-1);
	if (easing && !easing2) output$ = output$.map(easing);
	return output$.map(v => (max-min)*v+min);
}
function transition$(input$, timing, easing, interpolate) {
	if (!easing)
		easing = R.identity;
	if (typeof timing === 'number')
		timing = R.always(timing);
	if (!interpolate)
		interpolate = typeof d3 === 'object' ? d3.interpolate : ((a, b) => (rel) => rel*(b-a)+a);
	return O.create(o => {
		var start, current, goal, inter, first, timed;
		var init = R.once(() => {
			if (!frameHandle) tick();
		});
		var subscriptions = [
			input$.subscribe(input => {
				init();
				if (typeof start === 'undefined')
					o.onNext(current = input);
				start = current;
				goal = input;
				if (start === goal) return;
				inter = interpolate(start, goal);
				timed = timing(start, goal);
				first = null;
			}, e => o.onError(e), () => o.onCompleted()),
			time$.subscribe(time => {
				var rel = 1;
				if (current === goal) return;
				if (time >= (first||(first=time))+timed) {
					o.onNext(current = goal);
					return;
				}
				rel = ((time-first)/timed);
				if (easing) rel = easing(rel, start, goal);
				o.onNext(current = inter(rel));
			}),
		];
		return new Rx.BinaryDisposable(...subscriptions);
	}, input$);
};
function motivate$(input$, movement, continu) {
	return O.create(o => {
		var current, timer = new Timer(), newinput = NaN, lastoutput = NaN;
		var init = R.once(() => {
			if (!frameHandle) tick();
		});
		var subscriptions = [
			input$.subscribe(input => {
				init();
				newinput = input;
			}, e => o.onError(e), () => o.onCompleted()),
			time$.subscribe(time => {
				if (newinput === newinput) {
					if (!current) {
						current = Movement.Pause(newinput);
					} else {
						var timed = timer.get(time);
						if (timed !== timed) timed = 1;
						// TODO: account for gap between one ending and the next starting? eh
						current = movement(current.return(timed), newinput);
						timer.reset(time, current.duration);
						// timer.get(time) === 0
					}
					newinput = NaN;
				}
				if (!current) return;
				var timed = timer.get(time), output;
				if (timed !== timed && current.duration < Infinity) {
					output = current.next(current.duration).position;
					current = Movement.Pause(output);
				} else output = current.next(timed).position;
				if (output === output && output !== lastoutput) {
					//console.log(output, newinput, timer.get(time), current);
					o.onNext(lastoutput = output);
				}
			}),
		];
		return new Rx.BinaryDisposable(...subscriptions);
	}, input$);
}

function Timer() {
	this.start = NaN;
	this.duration = NaN;
}
Timer.prototype.reset = function resetTimer(time, duration) {
	if (duration < 1000/60)
		duration = 0;
	this.duration = duration;
	this.start = time;
}
Timer.prototype.get = function getTimer(time) {
	if (time > this.start + this.duration)
		return NaN;
	return (time-this.start);
}


