<?php

namespace Database\Seeders;

use App\Models\Job;
use Illuminate\Database\Seeder;

class JobSeeder extends Seeder
{
    public function run(): void
    {
        $jobs = [
            // Bekasi & Sekitarnya
            ['title' => 'Frontend Developer', 'company' => 'PT Digital Jaya', 'location' => 'Bekasi', 'type' => 'Full-time', 'salary_min' => 7000000, 'salary_max' => 10000000],
            ['title' => 'Backend Engineer (Laravel)', 'company' => 'CV Karya Bekasi', 'location' => 'Bekasi', 'type' => 'Full-time', 'salary_min' => 8000000, 'salary_max' => 12000000],
            ['title' => 'UI/UX Designer', 'company' => 'Studio Kreatif Bekasi', 'location' => 'Bekasi', 'type' => 'Contract', 'salary_min' => 5000000, 'salary_max' => 8000000],
            ['title' => 'Quality Assurance (QA)', 'company' => 'PT Inovasi Cikarang', 'location' => 'Bekasi', 'type' => 'Full-time', 'salary_min' => 6000000, 'salary_max' => 9000000],
            ['title' => 'DevOps Specialist', 'company' => 'PT Cloud Nusantara', 'location' => 'Bekasi', 'type' => 'Full-time', 'salary_min' => 10000000, 'salary_max' => 16000000],

            // Jakarta
            ['title' => 'Fullstack Web Developer', 'company' => 'PT Solusi Bangsaku', 'location' => 'Jakarta Selatan', 'type' => 'Full-time', 'salary_min' => 9000000, 'salary_max' => 14000000],
            ['title' => 'Mobile App Developer (Flutter)', 'company' => 'TechAsia Jakarta', 'location' => 'Jakarta Pusat', 'type' => 'Full-time', 'salary_min' => 8500000, 'salary_max' => 13000000],
            ['title' => 'Data Analyst', 'company' => 'PT Analytics Indonesia', 'location' => 'Jakarta Barat', 'type' => 'Full-time', 'salary_min' => 7500000, 'salary_max' => 11000000],
            ['title' => 'Product Manager', 'company' => 'Unicorn Startup', 'location' => 'Jakarta Selatan', 'type' => 'Full-time', 'salary_min' => 15000000, 'salary_max' => 25000000],
            ['title' => 'Cyber Security Analyst', 'company' => 'PT Aman Data', 'location' => 'Jakarta Timur', 'type' => 'Full-time', 'salary_min' => 11000000, 'salary_max' => 18000000],

            // Remote / Work From Home
            ['title' => 'React Native Developer', 'company' => 'Global Tech Inc', 'location' => 'Remote', 'type' => 'Full-time', 'salary_min' => 12000000, 'salary_max' => 20000000],
            ['title' => 'PHP Laravel Specialist', 'company' => 'Outsource Hub', 'location' => 'Remote', 'type' => 'Part-time', 'salary_min' => 6000000, 'salary_max' => 9000000],
            ['title' => 'Technical Writer', 'company' => 'DocuTech Services', 'location' => 'Remote', 'type' => 'Contract', 'salary_min' => 4500000, 'salary_max' => 7000000],
            ['title' => 'Golang Developer', 'company' => 'Fintech Startup', 'location' => 'Remote', 'type' => 'Full-time', 'salary_min' => 13000000, 'salary_max' => 22000000],
            ['title' => 'SEO Specialist', 'company' => 'Media Digital', 'location' => 'Remote', 'type' => 'Internship', 'salary_min' => 2500000, 'salary_max' => 4000000],

            // Bandung
            ['title' => 'Graphic Designer', 'company' => 'Kreatif Bandung Studio', 'location' => 'Bandung', 'type' => 'Full-time', 'salary_min' => 4500000, 'salary_max' => 7000000],
            ['title' => 'Laravel Backend Developer', 'company' => 'PT Tech Bandung', 'location' => 'Bandung', 'type' => 'Full-time', 'salary_min' => 6500000, 'salary_max' => 10000000],
            ['title' => 'Social Media Specialist', 'company' => 'Brandku Studio', 'location' => 'Bandung', 'type' => 'Part-time', 'salary_min' => 3500000, 'salary_max' => 5500000],

            // Surabaya & Kota Lain
            ['title' => 'Database Administrator', 'company' => 'PT Data Surabaya', 'location' => 'Surabaya', 'type' => 'Full-time', 'salary_min' => 8000000, 'salary_max' => 13000000],
            ['title' => 'IT Support Specialist', 'company' => 'CV Jatim Komputer', 'location' => 'Surabaya', 'type' => 'Full-time', 'salary_min' => 4000000, 'salary_max' => 6000000],
            ['title' => 'Network Engineer', 'company' => 'PT Jogja Network', 'location' => 'Yogyakarta', 'type' => 'Full-time', 'salary_min' => 5000000, 'salary_max' => 8500000],
            ['title' => 'Junior Web Developer', 'company' => 'PT Semesta Kode', 'location' => 'Semarang', 'type' => 'Internship', 'salary_min' => 3000000, 'salary_max' => 4500000],
            ['title' => 'System Analyst', 'company' => 'PT Bali Digital', 'location' => 'Denpasar', 'type' => 'Full-time', 'salary_min' => 9000000, 'salary_max' => 14000000],
            ['title' => 'Content Creator IT', 'company' => 'EduTech Nusantara', 'location' => 'Malang', 'type' => 'Part-time', 'salary_min' => 3500000, 'salary_max' => 5000000],
            ['title' => 'Machine Learning Engineer', 'company' => 'AI Innovation Lab', 'location' => 'Tangerang', 'type' => 'Full-time', 'salary_min' => 14000000, 'salary_max' => 24000000],
        ];

        foreach ($jobs as $job) {
            Job::create([
                'title' => $job['title'],
                'company' => $job['company'],
                'location' => $job['location'],
                'type' => $job['type'],
                'salary_min' => $job['salary_min'],
                'salary_max' => $job['salary_max'],
                'description' => "Dibutuhkan profesional untuk posisi {$job['title']} di {$job['company']}. Lingkungan kerja nyaman dan jenjang karir terbuka lebar.",
                'requirements' => "1. Menguasai skill terkait posisi {$job['title']}.\n2. Pengalaman kerja / porto yang relevan.\n3. Mampu bekerja dalam tim maupun individu.",
            ]);
        }
    }
}