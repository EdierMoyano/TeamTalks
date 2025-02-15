const menusItemsDropDown = document.querySelectorAll('.menu-item-dropdown');
const sidebar = document.getElementById('sidebar');
const menuBtn = document.getElementById('menu-btn');

menuBtn.addEventListener('click',()=>{
    sidebar.classList.toggle('minimize');
});

menusItemsDropDown.forEach((menuItem)=>{
    menuItem.addEventListener('click',()=>{
        const subMenu = menuItem.querySelector('.sub-menu');
         const isActive = menuItem.classList.toggle('sub-menu-toggle');
         if(subMenu){
            if(isActive){
                subMenu.style.height = ${subMenu.scrollHeight + 6};
                subMenu.style.padding = '0.2rem 0';
            }else{
                subMenu.style.height = '0';
                subMenu.style.padding = '0';
            }
         }
         menusItemsDropDown.forEach((item)=>{
            if(item !== menuItem){
                const otherSubmenu = item.querySelector('.sub-menu');
                if(otherSubmenu){
                    item.classList.remove('sub-menu-toggle');
                    otherSubmenu.style.height = '0';
                    otherSubmenu.style.padding = '0';
                }
            }
         })
    });
});