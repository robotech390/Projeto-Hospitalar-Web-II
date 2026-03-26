<?php

namespace App\Repositories;

interface LabDashboardRepositoryInterface
{
    public function getWeekData(): array;
    public function getRevenueData(): array;
}
