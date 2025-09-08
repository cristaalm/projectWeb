const forceActiveMenu = (route, pageName) => {
  route.forceActiveMenu = pageName
}

const findActiveMenu = (subMenu, route) => {
  let match = false
  subMenu.forEach(item => {
    if (
      ((route.forceActiveMenu !== undefined && item.pageName === route.forceActiveMenu) ||
                (route.forceActiveMenu === undefined && item.pageName === route.name)) &&
            !item.ignore
    ) {
      match = true
    } else if (!match && item.subMenu) {
      match = findActiveMenu(item.subMenu, route)
    }
  })
  
  return match
}

const nestedMenu = (menu, route) => {
  const formattedMenu = []

  menu.forEach(item => {
    if (typeof item !== 'string') {
      const menuItem = {
        icon: item.icon,
        title: item.title,
        pageName: item.pageName,
        subMenu: item.subMenu,
        ignore: item.ignore,
      }

      menuItem.active =
                ((route.forceActiveMenu !== undefined && menuItem.pageName === route.forceActiveMenu) ||
                    (route.forceActiveMenu === undefined && menuItem.pageName === route.name) ||
                    (menuItem.subMenu && findActiveMenu(menuItem.subMenu, route))) &&
                !menuItem.ignore

      if (menuItem.subMenu) {
        menuItem.activeDropdown = findActiveMenu(menuItem.subMenu, route)

        // Nested menu
        const subMenu = []

        nestedMenu(menuItem.subMenu, route).map(
          menu => typeof menu !== 'string' && subMenu.push(menu),
        )
        menuItem.subMenu = subMenu
      }

      formattedMenu.push(menuItem)
    } else {
      formattedMenu.push(item)
    }
  })

  return formattedMenu
}
