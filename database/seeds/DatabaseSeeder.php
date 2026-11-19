<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        /*$this->call(CountriesSeeder::class);
        $this->call(IDProvincesSeeder::class);
        $this->call(KotaKabupatenSeeder::class);*/

        $this->call(RolesSeeder::class);
        $this->call(ClubsSeeder::class);
        $this->call(MemberSourcesSeeder::class);
        $this->call(StaffsSeeder::class);

        /*$this->call(LeadsSeeder::class);*/

        $this->call(AppointmentStatusSeeder::class);
        $this->call(AppointmentTypesSeeder::class);
        $this->call(MembershipTypesSeeder::class);
        $this->call(AgreementRenewalTypesSeeder::class);
        $this->call(PaymentMethodsSeeder::class);
        $this->call(NoSaleReasonsSeeder::class);
        $this->call(WeighAndMeasureAttributesSeeder::class);
        $this->call(MemberAttributesSeeder::class);
        $this->call(DefaultSettingsSeeder::class);
        $this->call(FunctionalRolesSeeder::class);
        $this->call(TaxTypesSeeder::class);
        $this->call(ProductTypesSeeder::class);
        $this->call(VoucherTypesSeeder::class);
        $this->call(MemberHoldReasonsSeeder::class);
        $this->call(ClubServiceFeesSeeder::class);
        $this->call(TaxConfigsSeeder::class);
        $this->call(AccessMenusSeeder::class);
        $this->call(ChallengesSeeder::class);
        $this->call(ClubLoginsSeeder::class);
        $this->call(ThirtyDaysSeeder::class);
        $this->call(AutoNumbersSeeder::class);
        $this->call(VipMembershipSettingSeeder::class);
        $this->call(SizesSeeder::class);
        $this->call(AutoNumberWASeeder::class);
        $this->call(StaffManagementAccessSeeder::class);
        $this->call(TargetCategoriesSeeder::class);
        $this->call(PointSeeder::class);
        $this->call(AutoNumberAccountingJournalSeeder::class);

//        $this->call(BanksSeeder::class);
        $this->call(MainAccountSeeder::class);
        $this->call(ChartOfAccountSeeder::class);
        $this->call(DefaultCoaSeeder::class);
        $this->call(MasterPointOct19Seeder::class);
        $this->call(QuestionSeeder::class);
        $this->call(AutoNumberMemorialJournalSeeder::class);
        $this->call(JubilantSeeder::class);
        $this->call(AddJubilantToCOASeeder::class);
        $this->call(AddJubilantToClubInfoSeeder::class);
        $this->call(COAUpdateCreditSeeder::class);
        $this->call(HQCreditAutoNumberSeeder::class);
        $this->call(AutoNumberTempJournalSeeder::class);
        $this->call(CoaDefaultSeeder::class);
        $this->call(AutoNumberConsignmentOrderSeeder::class);
        $this->call(AutoNumberTransferAmortizationSeeder::class);
        $this->call(CoachDailyReportCategorySeeder::class);
    }
}
