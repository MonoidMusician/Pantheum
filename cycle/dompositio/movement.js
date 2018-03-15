function Movement(...props) {
	Object.assign(this, ...props);
	if (!this.duration) {
		console.log('invalid Movement: ', this);
		return Movement.Pause(this.next(0).position);
	}
	if (typeof this.position === 'number' && this.position !== this.next(0).position) {
		console.log('position did not match next(0)', this);
	}
	if (typeof this.goal === 'number' && this.goal !== this.next(1).position) {
		console.log('goal did not match next(1)', this);
	}
	renderd3(this);
}
Movement.Pause = function(position)  {
	return new Movement({
		type: 'pause',
		duration: Infinity,
		return(time) {
			return {
				position,
				velocity: 0,
			};
		},
		next(time) {
			return {position, velocity:0};
		},
	})
}
Movement.Relative = function(movement) {
	return new Movement({
		type: 'relative',
		duration: movement.duration,
		return(time) {
			return movement.return(time/movement.duration);
		},
		next(time) {
			return movement.next(time/movement.duration);
		}
	})
}
Movement.Linear = function({position, goal, duration}) {
	var displacement = goal-position;
	var velocity = displacement/duration;
	return new Movement.Relative({
		type: 'linear',
		duration,
		return(time) {
			return this.next(time);
		},
		next(time) {
			return {position:position+time*displacement, velocity};
		},
	})
}
Movement.Walk = function({position, goal, velocity}) {
	var displacement = goal-position;
	return new Movement.Relative({
		type: 'walk',
		duration: Math.abs(displacement)/velocity,
		return(time) {
			return this.next(time);
		},
		next(time) {
			return {position: position+time*displacement, velocity};
		},
	})
}
Movement.QuadIn = function({position, goal, velocity}) {
	var displacement = goal-position;
	return new Movement.Relative({
		type: 'quadin',
		duration: Math.abs(displacement)/velocity,
		return(time) {
			return this.next(time);
		},
		next(time) {
			return {
				position: position+time**2*displacement,
				velocity: 2*velocity*time,
			};
		},
	})
}
Movement.Accelerate = function({position, goal, velocity, acceleration, max_speed=Infinity}) {
	var displacement = goal-position;
	var accel = (goal > position ? acceleration : -acceleration);
	var inner_radical = velocity**2-2*accel*-displacement;
	if (Math.abs(inner_radical) < Number.EPSILON) inner_radical = 0;
	var radical = Math.sqrt(inner_radical);
	var duration = (-velocity+radical)/acceleration;
	//console.log(null, goal-position, velocity, radical, accel, duration);
	return new Movement({
		type: 'accelerate',
		duration,
		acceleration,
		return(time) {
			return this.next(time);
		},
		next(time) {
			return {
				position: position+velocity*time+accel*time**2/2,
				velocity: velocity+time*accel,
			};
		},
	})
}
Movement.Brake = function({position, goal, velocity}) {
	var displacement = goal-position;
	var duration = Math.abs(2*displacement/velocity);
	var acceleration = velocity/duration;
	return new Movement({
		type: 'brake',
		duration,
		return(time) {
			return this.next(time);
		},
		next(time) {
			return {
				position: position + velocity*time - acceleration/2*time**2,
				velocity: velocity - acceleration*time,
			}
		}
	})
}
Movement.DriveTo = function({position, goal, velocity, acceleration, max_speed}) {
	max_speed = Math.abs(max_speed);
	acceleration = Math.abs(acceleration);
	var speed = Math.abs(velocity);
	var displacement = goal-position;
	var sign = Math.sign(displacement);
	var accel = acceleration * sign;
	var max_velocity = max_speed * sign;
	var coast_t;
	//console.log({speed, displacement, sign, accel, max_velocity, max_speed, position, goal, velocity, acceleration});
	if (Math.sign(velocity) === sign && speed > 0) {
		var hard_decel_t = velocity/accel/2; // > 0
		var just_coast_t = displacement/velocity; // > 0
		if (just_coast_t < hard_decel_t) { // |a * Δx| < v**2/2
			//console.log('break:', just_coast_t, '<', hard_decel_t);
			return Movement.Brake({position, goal, velocity});
		}// else console.log('no brake:', just_coast_t, '>=', hard_decel_t);
	}// else console.log(Math.sign(velocity), sign, speed);
	var decel_t = Math.abs(max_velocity/acceleration);
	var decel_x = goal-decel_t**2*accel/2;
	var accel_t = velocity*sign < max_speed ? (max_velocity-velocity)/acceleration : 0;
	var accel_x = position+accel_t**2*accel/2+accel_t*velocity;
	if ((accel_x - decel_x)*sign > 0) {
		var dh = velocity**2/accel/2;
		var dt = velocity/accel;
		decel_t = Math.sqrt((dh+goal-position)/accel);
		accel_t = decel_t - dt;
		decel_x = goal-decel_t**2*accel/2;
		accel_x = position+accel_t**2*accel/2+accel_t*velocity;
		//console.log({dh, dt, decel_t, decel_x, accel_t, accel_x});
	}
	var coast_t = (decel_x - accel_x)/max_velocity;
	//console.log({decel_x, accel_t, accel_x, coast_t});
	var duration = accel_t + coast_t + decel_t;
	return new Movement({
		type: 'drive',
		duration,
		return(time) {
			return this.next(time);
		},
		next(time) {
			if (time < accel_t)
				return {
					position: position + velocity*time + accel/2*time**2,
					velocity: velocity + accel*time,
				}
			if (time <= accel_t + coast_t)
				return {
					position: accel_x + max_velocity*(time-accel_t),
					velocity: max_velocity,
				}
			return {
				position: goal - accel/2*(duration-time)**2,
				velocity: (duration-time) * accel,
			}
		}
	})
}
Movement.DriveThrough = function({position, goal, velocity, acceleration, max_speed}) {
	max_speed = Math.abs(max_speed);
	acceleration = Math.abs(acceleration);
	var speed = Math.abs(velocity);
	var displacement = goal-position;
	var sign = Math.sign(displacement);
	var accel = acceleration * Math.sign(max_speed-speed) * sign;
	var max_velocity = max_speed * sign;
	var accel_t = max_velocity-velocity && (max_velocity-velocity)/accel;
	var accel_x = position+accel_t**2*accel/2+accel_t*velocity;
	var coast_t = (goal - accel_x)/max_velocity;
	var duration = accel_t + coast_t;
	return new Movement({
		type: 'drive',
		duration,
		acceleration,
		return(time) {
			return this.next(time);
		},
		next(time) {
			if (time < accel_t)
				return {
					position: position + velocity*time + accel/2*time**2,
					velocity: velocity + accel*time,
				}
			return {
				position: accel_x + max_velocity*(time-accel_t),
				velocity: max_velocity,
			};
		}
	})
}
Movement.prototype.then = Movement.prototype.concat = function(...arg) {
	return Movement.Concat(this, ...arg);
}
Movement.Concat = function(...movements) {
	var l = movements.length;
	var I = t => R.clamp(0, l-1, Math.floor(t));
	var duration = R.sum(R.map(R.prop('duration'), movements));
	return new Movement({
		type: 'concat',
		movements,
		duration,
		return(time) {
			var i = 0, t = time;
			while (i < l-1 && t >= movements[i].duration)
			{ t -= movements[i].duration ; i++ }
			var b = movements[i];
			return b.return(t);
		},
		next(time) {
			var i = 0, t = time;
			while (i < l-1 && t >= movements[i].duration)
			{ t -= movements[i].duration ; i++ }
			var b = movements[i];
			return b.next(t);
		},
	})
}
Movement.prototype.bounce = function(loss=0.5, min=Infinity) {
	var acceleration = this.acceleration;
	var {position, velocity} = this.next(this.duration);
	velocity *= loss-1;
	var goal = position+(velocity**2)/2/acceleration;
	//console.log(position, goal, acceleration, velocity);
	var up = new Movement.Accelerate({position, goal, acceleration: -acceleration, velocity});
	var down = new Movement.Accelerate({position: goal, goal: position, acceleration, velocity: 0});
	if (!Number.isFinite(up.duration)) return this;
	//console.log(up, down);
	if (loss && Math.abs(velocity) > min) {
		return this.then(up, down.bounce(loss, min));
	} else return this.then(up, down);
}
