<?php

use App\StaticText;
use Illuminate\Database\Seeder;

class StaticTextsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        for ($i = 1; $i <= 4; $i++) {
//            factory(StaticText::class)->create([
//                'key' => 'footer_text_' . $i,
//            ]);
//        }

//        factory(StaticText::class)->create([
//            'name' => 'Описание сайта',
//            'key' => 'site_description',
//            'description' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.',
//        ]);

        factory(StaticText::class)->create([
            'name' => 'Info Aparto',
            'key' => 'info_aparto',
            'description' => 'Информация об Aparto',
        ]);

        factory(StaticText::class)->create([
            'name' => 'Адрес',
            'key' => 'contact_address',
            'description' => 'Узбекистан, Ташкент, ул.Навои, 1А',
        ]);
/*
        // footer text
        factory(StaticText::class)->create([
            'name' => 'Footer Text 1',
            'key' => 'footer_text_1',
            'description' => 'ООО "Optimize". 2020',
        ]);
        factory(StaticText::class)->create([
            'name' => 'Footer Text 2',
            'key' => 'footer_text_2',
            'description' => 'Номер свидетельства №00683211',
        ]);
        factory(StaticText::class)->create([
            'name' => 'Footer Text 3',
            'key' => 'footer_text_3',
            'description' => 'Все права защищены.',
        ]);
        factory(StaticText::class)->create([
            'name' => 'Footer Text 4',
            'key' => 'footer_text_4',
            'description' => 'Копирование материалов с сайта без согласования с администрацией ресурса запрещено',
        ]);

        // add items text
        factory(StaticText::class)->create([
            'name' => 'Текст "Добавить товар" на странице категории. Шаблоны: {category_name}',
            'key' => 'add_product_text',
            'description' => 'Добавить товар {category_name}',
        ]);
        factory(StaticText::class)->create([
            'name' => 'Текст "Добавить компанию" на странице рубрики компаний. Шаблоны: {rubric_name}',
            'key' => 'add_company_text',
            'description' => 'Добавить компанию {rubric_name}',
        ]);
        factory(StaticText::class)->create([
            'name' => 'Текст "Добавить услугу" на странице рубрики услуг. Шаблоны: {rubric_name}',
            'key' => 'add_service_text',
            'description' => 'Добавить услугу {rubric_name}',
        ]);
        factory(StaticText::class)->create([
            'name' => 'Текст "Добавить публикацию" на странице рубрики публикации. Шаблоны: {rubric_name}',
            'key' => 'add_publication_text',
            'description' => 'Добавить публикацию {rubric_name}',
        ]);
        factory(StaticText::class)->create([
            'name' => 'Текст "Добавить вакансию" на странице вакансии.',
            'key' => 'add_vacancy_text',
            'description' => 'Добавить вакансию',
        ]);
        factory(StaticText::class)->create([
            'name' => 'Текст "Добавить резюме" на странице резюме.',
            'key' => 'add_cv_text',
            'description' => 'Добавить резюме',
        ]);

        // SEO meta text Product
        factory(StaticText::class)->create([
            'name' => 'Meta title товара. Шаблоны: {name}',
            'key' => 'seo_template_product_seo_title',
            'description' => 'Product meta title {name}',
        ]);
        factory(StaticText::class)->create([
            'name' => 'Meta description товара. Шаблоны: {name}',
            'key' => 'seo_template_product_meta_description',
            'description' => 'Product meta description {name}',
        ]);
        factory(StaticText::class)->create([
            'name' => 'Meta keywords товара. Шаблоны: {name}',
            'key' => 'seo_template_product_meta_keywords',
            'description' => 'Product meta keywords {name}',
        ]);

        // SEO meta text Category
        factory(StaticText::class)->create([
            'name' => 'Meta title категории. Шаблоны: {name}',
            'key' => 'seo_template_category_seo_title',
            'description' => 'Category meta title {name}',
        ]);
        factory(StaticText::class)->create([
            'name' => 'Meta description категории. Шаблоны: {name}',
            'key' => 'seo_template_category_meta_description',
            'description' => 'Category meta description {name}',
        ]);
        factory(StaticText::class)->create([
            'name' => 'Meta keywords категории. Шаблоны: {name}',
            'key' => 'seo_template_category_meta_keywords',
            'description' => 'Category meta keywords {name}',
        ]);
        factory(StaticText::class)->create([
            'name' => 'Короткое описание категории. Шаблоны: {name}',
            'key' => 'seo_template_category_description',
            'description' => 'Category page description text {name}',
        ]);
        factory(StaticText::class)->create([
            'name' => 'Полное описание категории. Шаблоны: {name}',
            'key' => 'seo_template_category_body',
            'description' => 'Category page body text {name}',
        ]);

        // SEO meta text Company
        factory(StaticText::class)->create([
            'name' => 'Meta title компании. Шаблоны: {name}',
            'key' => 'seo_template_company_seo_title',
            'description' => 'Company meta title {name}',
        ]);
        factory(StaticText::class)->create([
            'name' => 'Meta description компании. Шаблоны: {name}',
            'key' => 'seo_template_company_meta_description',
            'description' => 'Company meta description {name}',
        ]);
        factory(StaticText::class)->create([
            'name' => 'Meta keywords компании. Шаблоны: {name}',
            'key' => 'seo_template_company_meta_keywords',
            'description' => 'Company meta keywords {name}',
        ]);

        // SEO meta text Rubric
        factory(StaticText::class)->create([
            'name' => 'Meta title рубрики компании. Шаблоны: {name}',
            'key' => 'seo_template_rubric_seo_title',
            'description' => 'Rubric meta title {name}',
        ]);
        factory(StaticText::class)->create([
            'name' => 'Meta description рубрики компании. Шаблоны: {name}',
            'key' => 'seo_template_rubric_meta_description',
            'description' => 'Rubric meta description {name}',
        ]);
        factory(StaticText::class)->create([
            'name' => 'Meta keywords рубрики компании. Шаблоны: {name}',
            'key' => 'seo_template_rubric_meta_keywords',
            'description' => 'Rubric meta keywords {name}',
        ]);
        factory(StaticText::class)->create([
            'name' => 'Короткое описание рубрики компании. Шаблоны: {name}',
            'key' => 'seo_template_rubric_description',
            'description' => 'Rubric page description text {name}',
        ]);
        factory(StaticText::class)->create([
            'name' => 'Полное описание рубрики компании. Шаблоны: {name}',
            'key' => 'seo_template_rubric_body',
            'description' => 'Rubric page body text {name}',
        ]);

        // SEO meta text Service
        factory(StaticText::class)->create([
            'name' => 'Meta title услуги. Шаблоны: {name}',
            'key' => 'seo_template_service_seo_title',
            'description' => 'Service meta title {name}',
        ]);
        factory(StaticText::class)->create([
            'name' => 'Meta description услуги. Шаблоны: {name}',
            'key' => 'seo_template_service_meta_description',
            'description' => 'Service meta description {name}',
        ]);
        factory(StaticText::class)->create([
            'name' => 'Meta keywords услуги. Шаблоны: {name}',
            'key' => 'seo_template_service_meta_keywords',
            'description' => 'Service meta keywords {name}',
        ]);

        // SEO meta text Serrubric
        factory(StaticText::class)->create([
            'name' => 'Meta title рубрики услуг. Шаблоны: {name}',
            'key' => 'seo_template_serrubric_seo_title',
            'description' => 'Serrubric meta title {name}',
        ]);
        factory(StaticText::class)->create([
            'name' => 'Meta description рубрики услуг. Шаблоны: {name}',
            'key' => 'seo_template_serrubric_meta_description',
            'description' => 'Serrubric meta description {name}',
        ]);
        factory(StaticText::class)->create([
            'name' => 'Meta keywords рубрики услуг. Шаблоны: {name}',
            'key' => 'seo_template_serrubric_meta_keywords',
            'description' => 'Serrubric meta keywords {name}',
        ]);
        factory(StaticText::class)->create([
            'name' => 'Короткое описание рубрики услуг. Шаблоны: {name}',
            'key' => 'seo_template_serrubric_description',
            'description' => 'Serrubric page description text {name}',
        ]);
        factory(StaticText::class)->create([
            'name' => 'Полное описание рубрики услуг. Шаблоны: {name}',
            'key' => 'seo_template_serrubric_body',
            'description' => 'Serrubric page body text {name}',
        ]);

        // SEO meta text Publication
        factory(StaticText::class)->create([
            'name' => 'Meta title публикации. Шаблоны: {name}',
            'key' => 'seo_template_publication_seo_title',
            'description' => 'Publication meta title {name}',
        ]);
        factory(StaticText::class)->create([
            'name' => 'Meta description публикации. Шаблоны: {name}',
            'key' => 'seo_template_publication_meta_description',
            'description' => 'Publication meta description {name}',
        ]);
        factory(StaticText::class)->create([
            'name' => 'Meta keywords публикации. Шаблоны: {name}',
            'key' => 'seo_template_publication_meta_keywords',
            'description' => 'Publication meta keywords {name}',
        ]);

        // SEO meta text Pubrubric
        factory(StaticText::class)->create([
            'name' => 'Meta title рубрики публикаций. Шаблоны: {name}',
            'key' => 'seo_template_pubrubric_seo_title',
            'description' => 'Pubrubric meta title {name}',
        ]);
        factory(StaticText::class)->create([
            'name' => 'Meta description рубрики публикаций. Шаблоны: {name}',
            'key' => 'seo_template_pubrubric_meta_description',
            'description' => 'Pubrubric meta description {name}',
        ]);
        factory(StaticText::class)->create([
            'name' => 'Meta keywords рубрики публикаций. Шаблоны: {name}',
            'key' => 'seo_template_pubrubric_meta_keywords',
            'description' => 'Pubrubric meta keywords {name}',
        ]);
        factory(StaticText::class)->create([
            'name' => 'Короткое описание рубрики публикаций. Шаблоны: {name}',
            'key' => 'seo_template_pubrubric_description',
            'description' => 'Pubrubric page description text {name}',
        ]);
        factory(StaticText::class)->create([
            'name' => 'Полное описание рубрики публикаций. Шаблоны: {name}',
            'key' => 'seo_template_pubrubric_body',
            'description' => 'Pubrubric page body text {name}',
        ]);

        // SEO meta text Vacancy
        factory(StaticText::class)->create([
            'name' => 'Meta title вакансии. Шаблоны: {name}',
            'key' => 'seo_template_vacancy_seo_title',
            'description' => 'Vacancy meta title {name}',
        ]);
        factory(StaticText::class)->create([
            'name' => 'Meta description вакансии. Шаблоны: {name}',
            'key' => 'seo_template_vacancy_meta_description',
            'description' => 'Vacancy meta description {name}',
        ]);
        factory(StaticText::class)->create([
            'name' => 'Meta keywords вакансии. Шаблоны: {name}',
            'key' => 'seo_template_vacancy_meta_keywords',
            'description' => 'Vacancy meta keywords {name}',
        ]);

        // SEO meta text
        factory(StaticText::class)->create([
            'name' => 'Meta title резюме. Шаблоны: {name}',
            'key' => 'seo_template_cv_seo_title',
            'description' => 'CV meta title {name}',
        ]);
        factory(StaticText::class)->create([
            'name' => 'Meta description резюме. Шаблоны: {name}',
            'key' => 'seo_template_cv_meta_description',
            'description' => 'CV meta description {name}',
        ]);
        factory(StaticText::class)->create([
            'name' => 'Meta keywords резюме. Шаблоны: {name}',
            'key' => 'seo_template_cv_meta_keywords',
            'description' => 'CV meta keywords {name}',
        ]);
*/
    }
}
