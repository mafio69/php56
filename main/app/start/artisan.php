<?php

/*
|--------------------------------------------------------------------------
| Register The Artisan Commands
|--------------------------------------------------------------------------
|
| Each available Artisan command must be registered with the console so
| that it is available to be called. We'll register every command so
| the console gets access to each of the command object instances.
|
*/

Artisan::add(new VoivodeshipMatch);
Artisan::add(new SynchronizeMobileEnvironment);
Artisan::add(new UpdateMantisVehicles);
Artisan::add(new ClearBeanstalkdQueueCommand);
Artisan::add(new TestSynchronization);
Artisan::add(new ImportLetters);
Artisan::add(new ImportInvoiceCommission);
Artisan::add(new InitCas);
Artisan::add(new GenerateEndingDate());
Artisan::add(new GenerateOldEndingDate());
Artisan::add(new ImportGetinVehicles());
Artisan::add(new FixMultipleVmanageVehicle());
Artisan::add(new GenerateServicesSheet());
Artisan::add(new ImportTempInjuries());
Artisan::add(new ImportTest());
Artisan::add(new ImportPermissions());
Artisan::add(new CheckCompaniesVat());
Artisan::add(new FetchCompanyAccountNumbers());
Artisan::add(new generateSeedCodeByModel());
Artisan::add(new FillDealersData());
Artisan::add(new GenerateEndingStepDates());
Artisan::add(new GenerateExampleDocuments());
Artisan::add(new FillForwardDate());
Artisan::add(new FillNipInBranches());
Artisan::add(new CopyInsurancesOnInjury());
Artisan::add(new CopySalesPrograms());
Artisan::add(new FixRedundantInjuryCompensations());
Artisan::add(new UpdateGapData());
Artisan::add(new RemoveLeasingAgreements());
Artisan::add(new TrimNipInBranches());
Artisan::add(new SapSzkodaPobierz());
Artisan::add(new FillSalesProgram());
Artisan::add(new FixMultiAttachesVehicleToInjury());
Artisan::add(new FillForwardAgainInvoices());
Artisan::add(new DebugOtherReport());
Artisan::add(new FillInjuryStepHistory());
Artisan::add(new FillTotalStatusSourceInInjury());
Artisan::add(new FixInjuryInvoicesBranch());
Artisan::add(new FillRegistrationAndProdDatesFromSyjon());
Artisan::add(new FillInjuryBranchesHistory());
Artisan::add(new FakeTask());
Artisan::add(new CheckForTaskEmails());
Artisan::add(new FixPolicyNettoBrutto());
Artisan::add(new DebugFillGroupForTasks());
Artisan::add(new GenerateFakeMobile());
Artisan::add(new CheckMailboxesConnection());
Artisan::add(new CopyInsurancesOnVmanageVehicles());
Artisan::add(new SyncSyjonDictionary());
Artisan::add(new GenerateSapReport());
Artisan::add(new GenerateArchiveSapError());
Artisan::add(new GenerateGeneralSapReport());
Artisan::add(new MoveLeasingAgreementInsuranceGroupRowIdToInsurance());
Artisan::add(new SystemRemoveTasks());
