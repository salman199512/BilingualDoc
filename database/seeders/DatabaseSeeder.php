<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Template;
use App\Models\TemplateField;
use App\Models\Document;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Admin User
        $admin = User::create([
            'name' => 'Admin Operator',
            'email' => 'admin@bilingual.com',
            'password' => Hash::make('password'),
        ]);

        // 2. Create Sample Bilingual Template
        $template = Template::create([
            'title' => 'Official Bilingual Circular (પરિપત્ર)',
            'description' => 'A standard bilingual circular template with placeholders for department names, dates, and subject details.',
            'user_id' => $admin->id,
            'html_content' => '
                <div style="text-align: center; font-weight: bold; margin-bottom: 20px;">
                    <p style="font-family: \'Times New Roman\'; font-size: 13pt; margin: 0;">DEPARTMENT OF REVENUE & FORESTS</p>
                    <p style="font-family: \'Noto Sans Gujarati\'; font-size: 13pt; margin: 0;">મહેસૂલ અને વન વિભાગ</p>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                    <div>
                        <p style="margin: 0;"><strong>Circular No / પરિપત્ર ક્રમાંક:</strong> [[circular_no]]</p>
                    </div>
                    <div style="text-align: right;">
                        <p style="margin: 0;"><strong>Date / તારીખ:</strong> [[circular_date]]</p>
                    </div>
                </div>
                <hr style="border: 0; border-top: 1px solid #000; margin-bottom: 20px;" />
                <div style="margin-bottom: 20px;">
                    <p style="margin: 0; text-align: center; font-weight: bold;">
                        <span style="font-family: \'Times New Roman\'; font-size: 13pt;">SUBJECT: </span>
                        <span style="font-family: \'Noto Sans Gujarati\'; font-size: 13pt;">[[subject_gujarati]]</span>
                        <span style="font-family: \'Times New Roman\'; font-size: 13pt;"> / [[subject_english]]</span>
                    </p>
                </div>
                <div style="margin-bottom: 20px; text-align: justify; line-height: 1.6;">
                    <p style="margin-bottom: 15px;">
                        <span style="font-family: \'Noto Sans Gujarati\'; font-size: 13pt;">આથી તમામ કર્મચારીઓને જણાવવામાં આવે છે કે </span>
                        <span style="font-family: \'Times New Roman\'; font-size: 13pt;">All employees are hereby informed that:</span>
                    </p>
                    <p style="margin-bottom: 15px; font-family: \'Noto Sans Gujarati\'; font-size: 13pt; margin-left: 20px;">
                        [[details_gujarati]]
                    </p>
                    <p style="margin-bottom: 15px; font-family: \'Times New Roman\'; font-size: 13pt; margin-left: 20px;">
                        [[details_english]]
                    </p>
                </div>
                <div style="margin-top: 50px; display: flex; justify-content: space-between;">
                    <div>
                        <p style="margin: 0; font-family: \'Noto Sans Gujarati\'; font-size: 13pt;">નકલ રવાના:</p>
                        <p style="margin: 0; font-family: \'Noto Sans Gujarati\'; font-size: 13pt;">૧. તમામ શાખા અધિકારીશ્રીઓ</p>
                    </div>
                    <div style="text-align: right;">
                        <p style="margin: 0; font-family: \'Noto Sans Gujarati\'; font-size: 13pt;"><strong>[[officer_name]]</strong></p>
                        <p style="margin: 0; font-family: \'Times New Roman\'; font-size: 13pt;">[[officer_designation]]</p>
                    </div>
                </div>
            ',
        ]);

        // Create fields for template
        TemplateField::create([
            'template_id' => $template->id,
            'field_key' => 'circular_no',
            'field_label' => 'Circular No / પરિપત્ર ક્રમાંક',
            'field_type' => 'text',
            'default_value' => 'REV/2026/102-A',
        ]);

        TemplateField::create([
            'template_id' => $template->id,
            'field_key' => 'circular_date',
            'field_label' => 'Circular Date / તારીખ',
            'field_type' => 'date',
            'default_value' => date('Y-m-d'),
        ]);

        TemplateField::create([
            'template_id' => $template->id,
            'field_key' => 'subject_gujarati',
            'field_label' => 'Subject in Gujarati (વિષય)',
            'field_type' => 'text',
            'default_value' => 'સરકારી કચેરીઓમાં દ્વિભાષી દસ્તાવેજીકરણ પદ્ધતિ ફરજિયાત કરવા બાબત.',
        ]);

        TemplateField::create([
            'template_id' => $template->id,
            'field_key' => 'subject_english',
            'field_label' => 'Subject in English',
            'field_type' => 'text',
            'default_value' => 'Regarding implementation of bilingual documentation in government offices.',
        ]);

        TemplateField::create([
            'template_id' => $template->id,
            'field_key' => 'details_gujarati',
            'field_label' => 'Details in Gujarati (વિગતો)',
            'field_type' => 'textarea',
            'default_value' => 'તમામ સરકારી પત્રવ્યવહાર અને સત્તાવાર પરિપત્રો માટે ગુજરાતી અને અંગ્રેજી બંને ભાષાઓનો એકસાથે ઉપયોગ કરવાનો રહેશે. ગુજરાતી લખાણ માટે Noto Sans Gujarati ફોન્ટ અને અંગ્રેજી લખાણ માટે Times New Roman ફોન્ટ વાપરવા સૂચના આપવામાં આવે છે.',
        ]);

        TemplateField::create([
            'template_id' => $template->id,
            'field_key' => 'details_english',
            'field_label' => 'Details in English',
            'field_type' => 'textarea',
            'default_value' => 'For all government correspondence and official circulars, both Gujarati and English languages must be used. It is instructed to use Noto Sans Gujarati font for Gujarati text and Times New Roman font for English text.',
        ]);

        TemplateField::create([
            'template_id' => $template->id,
            'field_key' => 'officer_name',
            'field_label' => 'Officer Name (અધિકારીનું નામ)',
            'field_type' => 'text',
            'default_value' => 'એ. કે. પટેલ',
        ]);

        TemplateField::create([
            'template_id' => $template->id,
            'field_key' => 'officer_designation',
            'field_label' => 'Officer Designation / હોદ્દો',
            'field_type' => 'text',
            'default_value' => 'Deputy Secretary (મહેસૂલ વિભાગ)',
        ]);

        // 3. Create Sample Document
        Document::create([
            'title' => 'Sample Bilingual Notice',
            'html_content' => '
                <div style="text-align: center; font-weight: bold; margin-bottom: 20px;">
                    <p style="font-family: \'Times New Roman\'; font-size: 13pt; margin: 0;">OFFICE OF THE COLLECTOR, AHMEDABAD</p>
                    <p style="font-family: \'Noto Sans Gujarati\'; font-size: 13pt; margin: 0;">કલેક્ટર કચેરી, અમદાવાદ</p>
                </div>
                <div style="margin-bottom: 20px; line-height: 1.6;">
                    <p style="margin-bottom: 10px;">
                        <span style="font-family: \'Times New Roman\'; font-size: 13pt;">All administrative departments must submit their reports by the end of this month.</span>
                    </p>
                    <p style="margin-bottom: 10px;">
                        <span style="font-family: \'Noto Sans Gujarati\'; font-size: 13pt;">તમામ વહીવટી વિભાગોએ ચાલુ માસના અંત સુધીમાં પોતાના અહેવાલો સબમિટ કરવાના રહેશે.</span>
                    </p>
                </div>
            ',
            'page_size' => 'A4',
            'orientation' => 'portrait',
            'font_gujarati' => 'Noto Sans Gujarati',
            'font_english' => 'Times New Roman',
            'margin_left' => 40,
            'margin_right' => 40,
            'margin_top' => 20,
            'margin_bottom' => 20,
            'status' => 'draft',
            'user_id' => $admin->id,
        ]);
    }
}
