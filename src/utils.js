/**
 * Test if Element is in window viewport
 * @param {jsx} element 
 * @return bool 
 */
export const isInViewport = (el) => {
	if (!el) return false;
  
	const elementRect = el.getBoundingClientRect();
	const viewportHeight = window.innerHeight || document.documentElement.clientHeight;
  
	return (
	  elementRect.top < viewportHeight &&
	  elementRect.bottom >= 0
	);
  };
  

