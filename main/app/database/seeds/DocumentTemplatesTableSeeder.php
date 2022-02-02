<?php


class DocumentTemplatesTableSeeder extends Seeder {

	public function run()
	{
        DocumentTemplate::create([
            'name' => 'bez papieru',
            'slug' => 'default'
        ]);
        DocumentTemplate::create([
            'name' => 'Wzór 1',
            'slug' => 'template_1'
        ]);
        DocumentTemplate::create([
            'name' => 'Wzór 2',
            'slug' => 'template_2'
        ]);
        DocumentTemplate::create([
            'name' => 'Wzór 3',
            'slug' => 'template_3'
        ]);
        DocumentTemplate::create([
            'name' => 'Wzór 4',
            'slug' => 'template_4'
        ]);
        DocumentTemplate::create([
            'name' => 'Wzór 5',
            'slug' => 'template_5'
        ]);
	}

}