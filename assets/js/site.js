document.querySelector('.menu')?.addEventListener('click',()=>{const n=document.querySelector('.navrow nav');n.style.display=n.style.display==='flex'?'none':'flex';n.style.position='absolute';n.style.top='72px';n.style.left='0';n.style.right='0';n.style.background='#fff';n.style.padding='20px';n.style.height='auto';n.style.flexDirection='column';n.style.alignItems='flex-start';});

// V25 mobile navigation
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.mobile-menu-v25').forEach(function(btn){
    var header=btn.closest('header');
    var nav=header ? header.querySelector('nav, .nav, .primary-nav') : null;
    if(!nav) return;
    btn.addEventListener('click', function(){
      var open=header.classList.toggle('mobile-nav-open-v25');
      btn.setAttribute('aria-expanded', open ? 'true' : 'false');
      btn.textContent=open ? 'CLOSE' : 'MENU';
      if(open){
        nav.style.display='flex';
        nav.style.position='absolute';
        nav.style.top='100%';
        nav.style.left='0';
        nav.style.right='0';
        nav.style.zIndex='1000';
        nav.style.background='#fff';
        nav.style.flexDirection='column';
        nav.style.alignItems='stretch';
        nav.style.padding='18px 5% 24px';
        nav.style.boxShadow='0 12px 24px rgba(0,0,0,.12)';
      } else {
        nav.removeAttribute('style');
      }
    });
  });
});
