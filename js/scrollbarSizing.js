(function() {
	var oldOverflow = document.documentElement.style.getPropertyValue('overflow');
	document.documentElement.style.setProperty('overflow', 'scroll');

	let scrollbar_height = (window.innerHeight - document.documentElement.offsetHeight);
	let scrollbar_width = (window.innerWidth - document.documentElement.offsetWidth);
	
	document.documentElement.style.setProperty('--scrollbar-height', scrollbar_height + 'px');
	document.documentElement.style.setProperty('--scrollbar-width', scrollbar_width + 'px');

	document.documentElement.style.setProperty('overflow', oldOverflow);
	
	window.scrollbarHeight = scrollbar_height;
	window.scrollbarWidth = scrollbar_width;
})();