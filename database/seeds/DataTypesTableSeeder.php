<?php

use App\Amenity;
use App\Apartment;
use App\Attraction;
use App\Audio;
use App\Article;
use App\Attribute;
use App\AttributeValue;
use App\Banner;
use App\Block;
use App\Brand;
use App\Category;
use App\Company;
use App\CV;
use App\FixedCompany;
use App\Floor;
use App\Gallery;
use App\Http\Controllers\Voyager\VoyagerProductController;
use App\Http\Controllers\Voyager\VoyagerUserController;
use App\Http\Controllers\Voyager\VoyagerRoleController;
use App\News;
use App\Order;
use App\Page;
use App\Photo;
use App\Pricelist;
use App\Product;
use App\ProductVariant;
use App\Project;
use App\Promotion;
use App\Publication;
use App\Pubrubric;
use App\Redirect;
use App\Restaurant;
use App\Review;
use App\Room;
use App\RoomType;
use App\Rubric;
use App\Serrubric;
use App\Service;
use App\Shop;
use App\SpecialEvent;
use App\Specialist;
use App\Specialization;
use App\StaticText;
use App\User;
use App\UserApplication;
use App\Vacancy;
use App\Video;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use TCG\Voyager\Models\DataType;
use TCG\Voyager\Models\Menu;
use TCG\Voyager\Models\Role;
use TCG\Voyager\Policies\UserPolicy;

class DataTypesTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('data_types')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $dataTypeItems = [
            // main rows
            [
                'slug'                  => 'users',
                'name'                  => 'users',
                'display_name_singular' => __('seeders.data_types.user.singular'),
                'display_name_plural'   => __('seeders.data_types.user.plural'),
                'icon'                  => 'voyager-person',
                'model_name'            => User::class,
                'controller'            => VoyagerUserController::class,
                'policy_name'           => UserPolicy::class,
                'server_side'           => 1,
                'details'               => [
                    "order_column" => 'status',
                    "order_display_column" => null,
                    "order_direction" => "desc",
                    "default_search_key" => null,
                    "scope" => null
                ],
            ],
            [
                'slug'                  => 'menus',
                'name'                  => 'menus',
                'display_name_singular' => __('seeders.data_types.menu.singular'),
                'display_name_plural'   => __('seeders.data_types.menu.plural'),
                'icon'                  => 'voyager-list',
                'model_name'            => Menu::class,
            ],
            [
                'slug'                  => 'roles',
                'name'                  => 'roles',
                'display_name_singular' => __('seeders.data_types.role.singular'),
                'display_name_plural'   => __('seeders.data_types.role.plural'),
                'icon'                  => 'voyager-lock',
                'model_name'            => Role::class,
                'controller'            => VoyagerRoleController::class,
            ],
            [
                'slug'                  => 'pages',
                'name'                  => 'pages',
                'display_name_singular' => __('seeders.data_types.page.singular'),
                'display_name_plural'   => __('seeders.data_types.page.plural'),
                'icon'                  => 'voyager-file-text',
                'model_name'            => Page::class,
            ],

            // additional rows
            [
                'slug'                  => 'banners',
                'name'                  => 'banners',
                'display_name_singular' => __('seeders.data_types.banner.singular'),
                'display_name_plural'   => __('seeders.data_types.banner.plural'),
                'icon'                  => 'voyager-images',
                'model_name'            => Banner::class,
            ],
            [
                'slug'                  => 'brands',
                'name'                  => 'brands',
                'display_name_singular' => __('seeders.data_types.brand.singular'),
                'display_name_plural'   => __('seeders.data_types.brand.plural'),
                'icon'                  => 'voyager-categories',
                'model_name'            => Brand::class,
            ],
            [
                'slug'                  => 'categories',
                'name'                  => 'categories',
                'display_name_singular' => __('seeders.data_types.category.singular'),
                'display_name_plural'   => __('seeders.data_types.category.plural'),
                'icon'                  => 'voyager-categories',
                'model_name'            => Category::class,
            ],
            [
                'slug'                  => 'companies',
                'name'                  => 'companies',
                'display_name_singular' => __('seeders.data_types.company.singular'),
                'display_name_plural'   => __('seeders.data_types.company.plural'),
                'icon'                  => 'voyager-company',
                'model_name'            => Company::class,
                'server_side'           => 1,
                'details'               => [
                    "order_column" => 'status',
                    "order_display_column" => null,
                    "order_direction" => "desc",
                    "default_search_key" => null,
                    "scope" => null
                ],
            ],
            [
                'slug'                  => 'products',
                'name'                  => 'products',
                'display_name_singular' => __('seeders.data_types.product.singular'),
                'display_name_plural'   => __('seeders.data_types.product.plural'),
                'icon'                  => 'voyager-basket',
                'model_name'            => Product::class,
                'controller'            => VoyagerProductController::class,
                'server_side'           => 1,
                'details'               => [
                    "order_column" => null,
                    "order_display_column" => null,
                    "order_direction" => "desc",
                    "default_search_key" => null,
                    "scope" => null
                ],
            ],
            [
                'slug'                  => 'product_variants',
                'name'                  => 'product_variants',
                'display_name_singular' => __('seeders.data_types.product_variant.singular'),
                'display_name_plural'   => __('seeders.data_types.product_variant.plural'),
                'icon'                  => 'voyager-lab',
                'model_name'            => ProductVariant::class,
                'server_side'           => 1,
            ],
            [
                'slug'                  => 'attributes',
                'name'                  => 'attributes',
                'display_name_singular' => __('seeders.data_types.attribute.singular'),
                'display_name_plural'   => __('seeders.data_types.attribute.plural'),
                'icon'                  => 'voyager-categories',
                'model_name'            => Attribute::class,
            ],
            [
                'slug'                  => 'attribute_values',
                'name'                  => 'attribute_values',
                'display_name_singular' => __('seeders.data_types.attribute_value.singular'),
                'display_name_plural'   => __('seeders.data_types.attribute_value.plural'),
                'icon'                  => 'voyager-wand',
                'model_name'            => AttributeValue::class,
            ],
            [
                'slug'                  => 'publications',
                'name'                  => 'publications',
                'display_name_singular' => __('seeders.data_types.publication.singular'),
                'display_name_plural'   => __('seeders.data_types.publication.plural'),
                'icon'                  => 'voyager-news',
                'model_name'            => Publication::class,
                'server_side'           => 1,
                'details'               => [
                    "order_column" => null,
                    "order_display_column" => null,
                    "order_direction" => "desc",
                    "default_search_key" => null,
                    "scope" => null
                ],
            ],
            [
                'slug'                  => 'rubrics',
                'name'                  => 'rubrics',
                'display_name_singular' => __('seeders.data_types.rubric.singular'),
                'display_name_plural'   => __('seeders.data_types.rubric.plural'),
                'icon'                  => 'voyager-categories',
                'model_name'            => Rubric::class,
            ],
            [
                'slug'                  => 'services',
                'name'                  => 'services',
                'display_name_singular' => __('seeders.data_types.service.singular'),
                'display_name_plural'   => __('seeders.data_types.service.plural'),
                'icon'                  => 'voyager-receipt',
                'model_name'            => Service::class,
                'server_side'           => 1,
                'details'               => [
                    "order_column" => null,
                    "order_display_column" => null,
                    "order_direction" => "desc",
                    "default_search_key" => null,
                    "scope" => null
                ],
            ],
            [
                'slug'                  => 'specializations',
                'name'                  => 'specializations',
                'display_name_singular' => __('seeders.data_types.specialization.singular'),
                'display_name_plural'   => __('seeders.data_types.specialization.plural'),
                'icon'                  => 'voyager-categories',
                'model_name'            => Specialization::class,
            ],
            [
                'slug'                  => 'specialists',
                'name'                  => 'specialists',
                'display_name_singular' => __('seeders.data_types.specialist.singular'),
                'display_name_plural'   => __('seeders.data_types.specialist.plural'),
                'icon'                  => 'voyager-people',
                'model_name'            => Specialist::class,
            ],
            [
                'slug'                  => 'articles',
                'name'                  => 'articles',
                'display_name_singular' => __('seeders.data_types.article.singular'),
                'display_name_plural'   => __('seeders.data_types.article.plural'),
                'icon'                  => 'voyager-documentation',
                'model_name'            => Article::class,
            ],
            [
                'slug'                  => 'news',
                'name'                  => 'news',
                'display_name_singular' => __('seeders.data_types.news.singular'),
                'display_name_plural'   => __('seeders.data_types.news.plural'),
                'icon'                  => 'voyager-news',
                'model_name'            => News::class,
            ],
            [
                'slug'                  => 'audios',
                'name'                  => 'audios',
                'display_name_singular' => __('seeders.data_types.audio.singular'),
                'display_name_plural'   => __('seeders.data_types.audio.plural'),
                'icon'                  => 'voyager-sound',
                'model_name'            => Audio::class,
            ],
            [
                'slug'                  => 'videos',
                'name'                  => 'videos',
                'display_name_singular' => __('seeders.data_types.video.singular'),
                'display_name_plural'   => __('seeders.data_types.video.plural'),
                'icon'                  => 'voyager-video',
                'model_name'            => Video::class,
            ],
            [
                'slug'                  => 'vacancies',
                'name'                  => 'vacancies',
                'display_name_singular' => __('seeders.data_types.vacancy.singular'),
                'display_name_plural'   => __('seeders.data_types.vacancy.plural'),
                'icon'                  => 'voyager-news',
                'model_name'            => Vacancy::class,
            ],
            [
                'slug'                  => 'c_v_s',
                'name'                  => 'c_v_s',
                'display_name_singular' => __('seeders.data_types.c_v.singular'),
                'display_name_plural'   => __('seeders.data_types.c_v.plural'),
                'icon'                  => 'voyager-file-text',
                'model_name'            => CV::class,
            ],
            [
                'slug'                  => 'galleries',
                'name'                  => 'galleries',
                'display_name_singular' => __('seeders.data_types.gallery.singular'),
                'display_name_plural'   => __('seeders.data_types.gallery.plural'),
                'icon'                  => 'voyager-photos',
                'model_name'            => Gallery::class,
            ],
            [
                'slug'                  => 'photos',
                'name'                  => 'photos',
                'display_name_singular' => __('seeders.data_types.photo.singular'),
                'display_name_plural'   => __('seeders.data_types.photo.plural'),
                'icon'                  => 'voyager-photo',
                'model_name'            => Photo::class,
                'server_side'           => 1,
                'details'               => [
                    "order_column" => null,
                    "order_display_column" => null,
                    "order_direction" => "desc",
                    "default_search_key" => null,
                    "scope" => null
                ],
            ],
            [
                'slug'                  => 'reviews',
                'name'                  => 'reviews',
                'display_name_singular' => __('seeders.data_types.review.singular'),
                'display_name_plural'   => __('seeders.data_types.review.plural'),
                'icon'                  => 'voyager-bubble',
                'model_name'            => Review::class,
                'server_side'           => 1,
                'details'               => [
                    "order_column" => 'status',
                    "order_display_column" => null,
                    "order_direction" => "desc",
                    "default_search_key" => null,
                    "scope" => null
                ],
            ],
            [
                'slug'                  => 'pricelists',
                'name'                  => 'pricelists',
                'display_name_singular' => __('seeders.data_types.pricelist.singular'),
                'display_name_plural'   => __('seeders.data_types.pricelist.plural'),
                'icon'                  => 'voyager-upload',
                'model_name'            => Pricelist::class,
                'server_side'           => 1,
                'details'               => [
                    "order_column" => 'status',
                    "order_display_column" => null,
                    "order_direction" => "desc",
                    "default_search_key" => null,
                    "scope" => null
                ],
            ],
            [
                'slug'                  => 'static_texts',
                'name'                  => 'static_texts',
                'display_name_singular' => __('seeders.data_types.static_text.singular'),
                'display_name_plural'   => __('seeders.data_types.static_text.plural'),
                'icon'                  => 'voyager-file-text',
                'model_name'            => StaticText::class,
            ],
            [
                'slug'                  => 'redirects',
                'name'                  => 'redirects',
                'display_name_singular' => __('seeders.data_types.redirect.singular'),
                'display_name_plural'   => __('seeders.data_types.redirect.plural'),
                'icon'                  => 'voyager-forward',
                'model_name'            => Redirect::class,
                'server_side'           => 1,
                'details'               => [
                    "order_column" => 'status',
                    "order_display_column" => null,
                    "order_direction" => "desc",
                    "default_search_key" => null,
                    "scope" => null
                ],
            ],
            [
                'slug'                  => 'orders',
                'name'                  => 'orders',
                'display_name_singular' => __('seeders.data_types.order.singular'),
                'display_name_plural'   => __('seeders.data_types.order.plural'),
                'icon'                  => 'voyager-shop',
                'model_name'            => Order::class,
                'server_side'           => 1,
            ],
            [
                'slug'                  => 'user_applications',
                'name'                  => 'user_applications',
                'display_name_singular' => __('seeders.data_types.user_application.singular'),
                'display_name_plural'   => __('seeders.data_types.user_application.plural'),
                'icon'                  => 'voyager-check-circle',
                'model_name'            => UserApplication::class,
                'server_side'           => 1,
            ],
            [
                'slug'                  => 'shops',
                'name'                  => 'shops',
                'display_name_singular' => __('seeders.data_types.shop.singular'),
                'display_name_plural'   => __('seeders.data_types.shop.plural'),
                'icon'                  => 'voyager-shop',
                'model_name'            => Shop::class,
                'server_side'           => 1,
            ],
            [
                'slug'                  => 'restaurants',
                'name'                  => 'restaurants',
                'display_name_singular' => __('seeders.data_types.restaurant.singular'),
                'display_name_plural'   => __('seeders.data_types.restaurant.plural'),
                'icon'                  => 'voyager-wineglass',
                'model_name'            => Restaurant::class,
            ],
            [
                'slug'                  => 'amenities',
                'name'                  => 'amenities',
                'display_name_singular' => __('seeders.data_types.amenity.singular'),
                'display_name_plural'   => __('seeders.data_types.amenity.plural'),
                'icon'                  => 'voyager-list',
                'model_name'            => Amenity::class,
                'server_side'           => 1,
            ],
            [
                'slug'                  => 'room_types',
                'name'                  => 'room_types',
                'display_name_singular' => __('seeders.data_types.room_type.singular'),
                'display_name_plural'   => __('seeders.data_types.room_type.plural'),
                'icon'                  => 'voyager-company',
                'model_name'            => RoomType::class,
            ],
            [
                'slug'                  => 'rooms',
                'name'                  => 'rooms',
                'display_name_singular' => __('seeders.data_types.room.singular'),
                'display_name_plural'   => __('seeders.data_types.room.plural'),
                'icon'                  => 'voyager-key',
                'model_name'            => Room::class,
                'server_side'           => 1,
            ],
            [
                'slug'                  => 'special_events',
                'name'                  => 'special_events',
                'display_name_singular' => __('seeders.data_types.special_event.singular'),
                'display_name_plural'   => __('seeders.data_types.special_event.plural'),
                'icon'                  => 'voyager-bell',
                'model_name'            => SpecialEvent::class,
            ],
            [
                'slug'                  => 'attractions',
                'name'                  => 'attractions',
                'display_name_singular' => __('seeders.data_types.attraction.singular'),
                'display_name_plural'   => __('seeders.data_types.attraction.plural'),
                'icon'                  => 'voyager-sun',
                'model_name'            => Attraction::class,
            ],
            [
                'slug'                  => 'projects',
                'name'                  => 'projects',
                'display_name_singular' => 'Проект',
                'display_name_plural'   => 'Проекты',
                'icon'                  => 'voyager-sun',
                'model_name'            => Project::class,
            ],
            [
                'slug'                  => 'blocks',
                'name'                  => 'blocks',
                'display_name_singular' => 'Блок',
                'display_name_plural'   => 'Блоки',
                'icon'                  => 'voyager-sun',
                'model_name'            => Block::class,
            ],
            [
                'slug'                  => 'floors',
                'name'                  => 'floors',
                'display_name_singular' => 'Этаж',
                'display_name_plural'   => 'Этажи',
                'icon'                  => 'voyager-sun',
                'model_name'            => Floor::class,
            ],
            [
                'slug'                  => 'apartments',
                'name'                  => 'apartments',
                'display_name_singular' => 'Помещение',
                'display_name_plural'   => 'Помещения',
                'icon'                  => 'voyager-sun',
                'model_name'            => Apartment::class,
            ],
        ];

        // seed data types
        $this->seedDataTypes($dataTypeItems);
    }
    /**
     * [dataType description].
     *
     * @param [type] $field [description]
     * @param [type] $for   [description]
     *
     * @return [type] [description]
     */
    protected function dataType($field, $for)
    {
        return DataType::firstOrNew([$field => $for]);
    }

    /*
     * Seed all data types
     *
     * @param [array] $dataTypeItems
     *
     */
    private function seedDataTypes(array $dataTypeItems)
    {
        $modules = config('cms.modules');
        foreach ($dataTypeItems as $item) {
            if (empty($modules[$item['slug']])) {
                continue;
            }
            $dataType = $this->dataType('slug', $item['slug']);
            if (!$dataType->exists) {
                $dataType->fill([
                    'name'                  => $item['slug'],
                    'display_name_singular' => $item['display_name_singular'],
                    'display_name_plural'   => $item['display_name_plural'],
                    'icon'                  => $item['icon'],
                    'model_name'            => $item['model_name'],
                    'controller'            => $item['controller'] ?? null,
                    'policy_name'           => $item['policy_name'] ?? null,
                    'generate_permissions'  => $item['generate_permissions'] ?? 1,
                    'description'           => $item['description'] ?? null,
                    'server_side'           => $item['server_side'] ?? 0,
                    'details'               => $item['details'] ?? null,
                ])->save();
            }
        }
    }
}
