document.addEventListener('DOMContentLoaded', () => {
    // Function to fetch categories from the database
    async function loadCategories() {
        try {
            const response = await fetch('get_categories.php');
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            const data = await response.json();
            const menuCategory = document.getElementById('menu-category');

            function buildMenu(categories, parentElement) {
                categories.forEach(category => {
                    const li = document.createElement('li');
                    li.textContent = category.name;

                    const subMenu = document.createElement('ul');
                    subMenu.classList.add('dropdown-menu');
                    buildMenu(category.children, subMenu);

                    if (subMenu.children.length > 0) {
                        li.appendChild(subMenu);
                    }

                    parentElement.appendChild(li);
                });
            }

            buildMenu(data, menuCategory);
        } catch (error) {
            console.error('Error fetching categories:', error);
        }
    }

    // Function to handle user menu
    function handleUserMenu() {
        const userIcon = document.getElementById('user-icon');
        // Simulate fetching user data
        const user = {
            loggedIn: true,
            isAdmin: true,
            isMember: true,
        };

        if (user.loggedIn) {
            let menuHtml = `<a href="profile.php">Hồ Sơ</a>`;

            if (user.isAdmin) {
                menuHtml += `<a href="admin.php">Quản trị</a>`;
            }

            if (user.isMember) {
                menuHtml += `<a href="sinhvat.php">Sinh vật</a>`;
            }

            menuHtml += `<a href="logout.php">Đăng xuất</a>`;

            userIcon.innerHTML = menuHtml;
        } else {
            userIcon.innerHTML = `<a href="login.php">Đăng nhập</a> <a href="register.php">Đăng ký</a>`;
        }
    }

    loadCategories();
    handleUserMenu();
});