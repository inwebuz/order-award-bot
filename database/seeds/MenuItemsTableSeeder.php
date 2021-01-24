<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use TCG\Voyager\Models\Menu;
use TCG\Voyager\Models\MenuItem;

class MenuItemsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('menu_items')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $menu = Menu::where('name', 'admin')->firstOrFail();

        $menuItems = [
            // main items
            [
                'title' => __('seeders.menu_items.dashboard'),
                'icon' => 'voyager-boat',
                'route' => 'voyager.dashboard',
            ],
            [
                'title' => __('seeders.menu_items.media'),
                'icon' => 'voyager-images',
                'route' => 'voyager.media.index',
            ],
            [
                'slug' => 'users',
                'icon' => 'voyager-person',
            ],
            [
                'slug' => 'roles',
                'icon' => 'voyager-lock',
            ],
            [
                'title' => __('seeders.menu_items.tools'),
                'icon' => 'voyager-tools',
                'route' => '',
                'submenu_items' => [
                    [
                        'title' => __('seeders.menu_items.menu_builder'),
                        'icon' => 'voyager-list',
                        'route' => 'voyager.menus.index',
                    ],
                    [
                        'title' => __('seeders.menu_items.database'),
                        'icon' => 'voyager-data',
                        'route' => 'voyager.database.index',
                    ],
                    [
                        'title' => __('seeders.menu_items.compass'),
                        'icon' => 'voyager-compass',
                        'route' => 'voyager.compass.index',
                    ],
                    [
                        'title' => __('seeders.menu_items.bread'),
                        'icon' => 'voyager-bread',
                        'route' => 'voyager.bread.index',
                    ],
                ],
            ],
            [
                'title' => __('seeders.menu_items.settings'),
                'icon' => 'voyager-settings',
                'route' => 'voyager.settings.index',
            ],
            [
                'slug' => 'pages',
                'icon' => 'voyager-file-text',
            ],

            // additional items
            [
                'slug' => 'banners',
                'icon' => 'voyager-images',
            ],
            [
                'slug' => 'promotions',
                'icon' => 'voyager-images',
            ],
            [
                'slug' => 'categories',
                'icon' => 'voyager-categories',
            ],
            [
                'slug' => 'brands',
                'icon' => 'voyager-categories',
            ],
            [
                'slug' => 'products',
                'icon' => 'voyager-basket',
            ],
            [
                'slug' => 'product_variants',
                'icon' => 'voyager-lab',
            ],
            [
                'slug' => 'pricelists',
                'icon' => 'voyager-upload',
            ],
            [
                'slug' => 'attributes',
                'icon' => 'voyager-categories',
            ],
            [
                'slug' => 'attribute_values',
                'icon' => 'voyager-wand',
            ],
            [
                'slug' => 'rubrics',
                'icon' => 'voyager-categories',
            ],
            [
                'slug' => 'companies',
                'icon' => 'voyager-company',
            ],
            [
                'slug' => 'pubrubrics',
                'icon' => 'voyager-categories',
            ],
            [
                'slug' => 'publications',
                'icon' => 'voyager-news',
            ],
            [
                'slug' => 'articles',
                'icon' => 'voyager-documentation',
            ],
            [
                'slug' => 'news',
                'icon' => 'voyager-news',
            ],
            [
                'slug' => 'serrubrics',
                'icon' => 'voyager-categories',
            ],
            [
                'slug' => 'services',
                'icon' => 'voyager-receipt',
            ],
            [
                'slug' => 'specializations',
                'icon' => 'voyager-categories',
            ],
            [
                'slug' => 'specialists',
                'icon' => 'voyager-people',
            ],
            [
                'slug' => 'vacancies',
                'icon' => 'voyager-news',
            ],
            [
                'slug' => 'c_v_s',
                'icon' => 'voyager-file-text',
            ],
            [
                'slug' => 'audios',
                'icon' => 'voyager-sound',
            ],
            [
                'slug' => 'videos',
                'icon' => 'voyager-video',
            ],
            [
                'slug' => 'galleries',
                'icon' => 'voyager-photos',
            ],
            [
                'slug' => 'photos',
                'icon' => 'voyager-photo',
            ],
            [
                'slug' => 'reviews',
                'icon' => 'voyager-bubble',
            ],
            [
                'slug' => 'static_texts',
                'icon' => 'voyager-file-text',
            ],
            [
                'slug' => 'redirects',
                'icon' => 'voyager-forward',
            ],
            [
                'slug' => 'fixed_companies',
                'icon' => 'voyager-paperclip',
            ],
            [
                'slug' => 'orders',
                'icon' => 'voyager-shop',
            ],
            [
                'slug' => 'user_applications',
                'icon' => 'voyager-check-circle',
            ],
            [
                'slug' => 'shops',
                'icon' => 'voyager-shop',
            ],
            [
                'slug' => 'restaurants',
                'icon' => 'voyager-wineglass',
            ],
            [
                'slug' => 'amenities',
                'icon' => 'voyager-list',
            ],
            [
                'slug' => 'room_types',
                'icon' => 'voyager-company',
            ],
            [
                'slug' => 'rooms',
                'icon' => 'voyager-key',
            ],
            [
                'slug' => 'special_events',
                'icon' => 'voyager-bell',
            ],
            [
                'slug' => 'attractions',
                'icon' => 'voyager-sun',
            ],
            [
                'title' => 'Проекты',
                'slug' => 'projects',
                'icon' => 'voyager-sun',
            ],
            [
                'title' => 'Блоки',
                'slug' => 'blocks',
                'icon' => 'voyager-sun',
            ],
            [
                'title' => 'Этажи',
                'slug' => 'floors',
                'icon' => 'voyager-sun',
            ],
            [
                'title' => 'Помещения',
                'slug' => 'apartments',
                'icon' => 'voyager-sun',
            ],
        ];

        // seed menu
        $this->seedMenuItems($menu, $menuItems);
    }

    private function seedMenuItems(Menu $menu, array $menuItems)
    {
        $modules = config('cms.modules');

        foreach ($menuItems as $key => $item) {
            if (!empty($item['slug']) && empty($modules[$item['slug']])) {
                continue;
            }
            $order = $key + 1;
            $route = isset($item['route']) ? $item['route'] : 'voyager.' . $item['slug'] . '.index';
            $title = isset($item['title']) ? $item['title'] : __('seeders.menu_items.' . $item['slug']);
            $menuItem = MenuItem::firstOrNew([
                'menu_id' => $menu->id,
                'url' => '',
                'route' => $route,
            ]);
            if (!$menuItem->exists) {
                $menuItem->fill([
                    'title' => $title,
                    'target' => '_self',
                    'icon_class' => $item['icon'],
                    'color' => null,
                    'parent_id' => null,
                    'order' => $order,
                ])->save();
            }
            if (isset($item['submenu_items']) && is_array($item['submenu_items'])) {
                foreach ($item['submenu_items'] as $subKey => $subItem) {
                    $subRoute = isset($subItem['route']) ? $subItem['route'] : 'voyager.' . $subItem['slug'] . '.index';
                    $subTitle = isset($subItem['title']) ? $subItem['title'] : __('seeders.menu_items.' . $subItem['slug']);
                    $subMenuItem = MenuItem::firstOrNew([
                        'menu_id' => $menu->id,
                        'url' => '',
                        'route' => $subRoute,
                    ]);
                    if (!$subMenuItem->exists) {
                        $subMenuItem->fill([
                            'title' => $subTitle,
                            'target' => '_self',
                            'icon_class' => $subItem['icon'],
                            'color' => null,
                            'parent_id' => $menuItem->id,
                            'order' => $order * 100 + $subKey + 1,
                        ])->save();
                    }
                }
            }
        }
    }
}
