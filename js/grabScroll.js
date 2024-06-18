const E_MOUSE_BTNS = Object.freeze({
	LEFT: 0,
	MIDDLE: 1,
	RIGHT: 2
});

class grabScroll {
	static walkX = 2;
	static walkY = 2;

	_parent = null;
	startX = 0;
	startY = 0;
	scrollTop = 0;
	scrollLeft = 0;
	isDown = false;
	anyButton = false;
	buttons = [E_MOUSE_BTNS.LEFT];

	constructor(target) {
		this._parent = target;
		this._parent.addEventListener("mousedown", (e) => this.mouseDown(e));
		this._parent.addEventListener("mouseup", (e) => this.mouseUp(e));
		this._parent.addEventListener("mouseleave", (e) => this.mouseLeave(e));
		this._parent.addEventListener("mousemove", (e) => this.mouseMove(e));
	}

	mouseDown(e) {
		if(this.anyButton || (!this.anyButton && this.buttons.includes(e.button))) {
			this.isDown = true;
			this.startY = e.pageY - this._parent.offsetTop;
			this.startX = e.pageX - this._parent.offsetLeft;
			this.scrollTop = this._parent.scrollTop;
			this.scrollLeft = this._parent.scrollLeft;
		}
	}

	mouseUp(e) {
		this.isDown = false;
	}

	mouseLeave(e) {
		this.isDown = false;
	}

	mouseMove(e) {
		if(this.isDown) {
			e.preventDefault();

			const y = e.pageY - this._parent.offsetTop;
			const x = e.pageX - this._parent.offsetLeft;

			const walkY = (y - this.startY) * grabScroll.walkX;
			const walkX = (x - this.startX) * grabScroll.walkY;

			this._parent.scrollTop = this.scrollTop - walkY;
			this._parent.scrollLeft = this.scrollLeft - walkX;
		}
	}
	
	addButton(btn) {
		if(!this.buttons.includes(btn))
			this.buttons.push(btn);
	}
	
	removeButton(btn) {
		delete this.buttons[this.buttons.indexOf(btn)];
	}
}