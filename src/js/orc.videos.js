const showVideo = ((video) => {
	document.querySelector('#video').classList.add('show');
	document.querySelector('#overlay').classList.add('show');
	document.querySelector('#video-container').innerHTML = `<iframe width=560 height=315 src=https://www.youtube.com/embed/${video}?rel=0 frameborder=0 allowfullscreen></iframe>`;
});

const stopVideo = ((e) => {
	if ('video' !== e.target.id) {
		document.querySelector('#video').classList.remove('show');
		document.querySelector('#overlay').classList.remove('show');
		document.querySelector('#video-container').innerHTML = '';
	}
});

const mainVideo = document.querySelector('.mainVideo');
if (mainVideo) {
	mainVideo.addEventListener('click', showVideo.bind(null, videourls.mainVideo), false)
}

const xmasVideo = document.querySelector('.xmasVideo');
if (xmasVideo) {
	xmasVideo.addEventListener('click', showVideo.bind(null, videourls.xmasVideo), false)
}

document.addEventListener('touchend', stopVideo, false);
document.addEventListener('mouseup', stopVideo, false);
