<template>
    <div>

        <v-overlay :value="overlay">
            <v-progress-circular indeterminate size="64"></v-progress-circular>
        </v-overlay>

        <v-container>

            <h1 class="text-center">Assessor Information</h1>

            <v-card class="mb-1rem">
                <v-card-actions
                        class="flex flex-col"
                >
                    <div class="flex justify-content-around w-full">

                        <div class="flex flex-col nav-icon-spacing">
                            <v-icon
                                    :color="show.owner ? 'success': ''"
                                    class="nav-btn-position"
                                    @click="showSection('owner')"

                            >mdi-details
                            </v-icon>
                            <div class="nav-icon-label" :class="show.owner ? 'nav-icon-label-selected': ''">
                                Owner
                            </div>
                        </div>

                        <div class="flex flex-col nav-icon-spacing">
                            <v-icon
                                    :color="show.valuations ? 'success': ''"
                                    class="nav-btn-position"
                                    @click="showSection('valuations')"

                            >mdi-details
                            </v-icon>
                            <div class="nav-icon-label" :class="show.valuations ? 'nav-icon-label-selected': ''">
                                Valuations
                            </div>
                        </div>

                        <div class="flex flex-col nav-icon-spacing">
                            <v-icon
                                    :color="show.propertyData ? 'success': ''"
                                    class="nav-btn-position"
                                    @click="showSection('propertyData')"

                            >mdi-details
                            </v-icon>
                            <div class="nav-icon-label" :class="show.propertyData ? 'nav-icon-label-selected': ''">
                                General Data
                            </div>
                        </div>

                        <div class="flex flex-col nav-icon-spacing">
                            <v-icon
                                    :color="show.similarParcels ? 'success': ''"
                                    class="nav-btn-position"
                                    @click="showSection('similarParcels')"

                            >mdi-details
                            </v-icon>
                            <div class="nav-icon-label" :class="show.similarParcels ? 'nav-icon-label-selected': ''">
                                Similar
                            </div>
                        </div>

                        <div class="flex flex-col nav-icon-spacing">
                            <v-icon
                                    :color="show.residentialPropertyData ? 'success': ''"
                                    class="nav-btn-position"
                                    @click="showSection('residentialPropertyData')"

                            >mdi-details
                            </v-icon>
                            <div class="nav-icon-label"
                                 :class="show.residentialPropertyData ? 'nav-icon-label-selected': ''">
                                Property Data
                            </div>
                        </div>

                    </div>
                </v-card-actions>
            </v-card>

            <v-card v-if="show.valuations">
                <v-card-title>Valuations</v-card-title>
                <v-card-text>

                    <v-data-iterator
                            :items="locationData.Valuations"
                            hide-default-footer
                    >
                        <template v-slot:default="props">
                            <v-row>
                                <v-col
                                        v-for="item in props.items"
                                        :key="item.name"
                                        cols="12"
                                        sm="6"
                                        md="4"
                                        lg="3"
                                >
                                    <v-card>
                                        <v-card-title class="subheading font-weight-bold">{{ item.TaxYear }}
                                        </v-card-title>

                                        <v-divider></v-divider>

                                        <v-list dense>
                                            <v-list-item>
                                                <v-list-item-content>Full Cash Value:</v-list-item-content>
                                                <v-list-item-content class="align-end">{{ item.FullCashValue }}
                                                </v-list-item-content>
                                            </v-list-item>

                                            <v-list-item>
                                                <v-list-item-content>Limited Property Value:</v-list-item-content>
                                                <v-list-item-content class="align-end">{{ item.LimitedPropertyValue }}
                                                </v-list-item-content>
                                            </v-list-item>

                                            <v-list-item>
                                                <v-list-item-content>Legal Classification Code:</v-list-item-content>
                                                <v-list-item-content class="align-end">{{ item.LegalClassificationCode
                                                    }}
                                                </v-list-item-content>
                                            </v-list-item>

                                            <v-list-item>
                                                <v-list-item-content>Legal Classification:</v-list-item-content>
                                                <v-list-item-content class="align-end">{{ item.LegalClassification }}
                                                </v-list-item-content>
                                            </v-list-item>

                                            <v-list-item>
                                                <v-list-item-content>Assessment Ratio Percentage:</v-list-item-content>
                                                <v-list-item-content class="align-end">{{ item.AssessmentRatioPercentage
                                                    }}
                                                </v-list-item-content>
                                            </v-list-item>

                                            <v-list-item>
                                                <v-list-item-content>Assessed FCV:</v-list-item-content>
                                                <v-list-item-content class="align-end">{{ item.AssessedFCV }}
                                                </v-list-item-content>
                                            </v-list-item>

                                            <v-list-item>
                                                <v-list-item-content>Assessed LPV:</v-list-item-content>
                                                <v-list-item-content class="align-end">{{ item.AssessedLPV }}
                                                </v-list-item-content>
                                            </v-list-item>

                                            <v-list-item>
                                                <v-list-item-content>Valuation Source:</v-list-item-content>
                                                <v-list-item-content class="align-end">{{ item.ValuationSource }}
                                                </v-list-item-content>
                                            </v-list-item>

                                            <v-list-item>
                                                <v-list-item-content>PE Prop Use Desc:</v-list-item-content>
                                                <v-list-item-content class="align-end">{{ item.PEPropUseDesc }}
                                                </v-list-item-content>
                                            </v-list-item>

                                            <v-list-item>
                                                <v-list-item-content>Property Use Code:</v-list-item-content>
                                                <v-list-item-content class="align-end">{{ item.PropertyUseCode }}
                                                </v-list-item-content>
                                            </v-list-item>

                                            <v-list-item>
                                                <v-list-item-content>Notice Of Change Indicator:</v-list-item-content>
                                                <v-list-item-content class="align-end">{{ item.NoticeOfChangeIndicator
                                                    }}
                                                </v-list-item-content>
                                            </v-list-item>

                                            <v-list-item>
                                                <v-list-item-content>Tax Area Code:</v-list-item-content>
                                                <v-list-item-content class="align-end">{{ item.TaxAreaCode }}
                                                </v-list-item-content>
                                            </v-list-item>

                                        </v-list>
                                    </v-card>
                                </v-col>
                            </v-row>
                        </template>
                    </v-data-iterator>

                </v-card-text>
                <v-card-actions>

                </v-card-actions>
            </v-card>

            <v-card v-if="show.residentialPropertyData">
                <v-card-title>Residential Property Data</v-card-title>
                <v-card-text>
                    <div>

                        <v-divider></v-divider>

                        <v-list dense>

                            <v-list-item class="flex justify-content-between">
                                <v-list-item-content>Area Neighboorhood:</v-list-item-content>
                                <v-list-item-content class="align-end">{{
                                    locationData.ResidentialPropertyData.AreaNeighboorhood }}
                                </v-list-item-content>
                            </v-list-item>

                            <v-list-item class="flex justify-content-between">
                                <v-list-item-content>Assessor Market:</v-list-item-content>
                                <v-list-item-content class="align-end">{{
                                    locationData.ResidentialPropertyData.AssessorMarket }}
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item class="flex justify-content-between">
                                <v-list-item-content>Detached Living Area:</v-list-item-content>
                                <v-list-item-content class="align-end">{{
                                    locationData.ResidentialPropertyData.DetachedLivingArea }}
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item class="flex justify-content-between">
                                <v-list-item-content>Main Living Area:</v-list-item-content>
                                <v-list-item-content class="align-end">{{
                                    locationData.ResidentialPropertyData.MainLivingArea }}
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item class="flex justify-content-between">
                                <v-list-item-content>Number Of Carports:</v-list-item-content>
                                <v-list-item-content class="align-end">{{
                                    locationData.ResidentialPropertyData.NumberOfCarports }}
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item class="flex justify-content-between">
                                <v-list-item-content>Full Cash Value:</v-list-item-content>
                                <v-list-item-content class="align-end">{{
                                    locationData.ResidentialPropertyData.FullCashValue }}
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item class="flex justify-content-between">
                                <v-list-item-content>Number Of Covered Patios:</v-list-item-content>
                                <v-list-item-content class="align-end">{{
                                    locationData.ResidentialPropertyData.NumberOfCoveredPatios }}
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item class="flex justify-content-between">
                                <v-list-item-content>Number Of Garages:</v-list-item-content>
                                <v-list-item-content class="align-end">{{
                                    locationData.ResidentialPropertyData.NumberOfGarages }}
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item class="flex justify-content-between">
                                <v-list-item-content>Number Of Patios:</v-list-item-content>
                                <v-list-item-content class="align-end">{{
                                    locationData.ResidentialPropertyData.NumberofPatios }}
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item class="flex justify-content-between">
                                <v-list-item-content>Number Of Uncovered Patios:</v-list-item-content>
                                <v-list-item-content class="align-end">{{
                                    locationData.ResidentialPropertyData.NumberOfUncoveredPatios }}
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item class="flex justify-content-between">
                                <v-list-item-content>Construction Year:</v-list-item-content>
                                <v-list-item-content class="align-end">{{
                                    locationData.ResidentialPropertyData.ConstructionYear }}
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item class="flex justify-content-between">
                                <v-list-item-content>Improvement Quality Grade:</v-list-item-content>
                                <v-list-item-content class="align-end">{{
                                    locationData.ResidentialPropertyData.ImprovementQualityGrade }}
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item class="flex justify-content-between">
                                <v-list-item-content>PE Improvement Quality Grade:</v-list-item-content>
                                <v-list-item-content class="align-end">{{
                                    locationData.ResidentialPropertyData.PEImprovementQualityGrade }}
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item class="flex justify-content-between">
                                <v-list-item-content>Bath Fixtures:</v-list-item-content>
                                <v-list-item-content class="align-end">{{
                                    locationData.ResidentialPropertyData.BathFixtures }}
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item class="flex justify-content-between">
                                <v-list-item-content>Covered Parking:</v-list-item-content>
                                <v-list-item-content class="align-end">{{
                                    locationData.ResidentialPropertyData.CoveredParking }}
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item class="flex justify-content-between">
                                <v-list-item-content>Parking Type:</v-list-item-content>
                                <v-list-item-content class="align-end">{{
                                    locationData.ResidentialPropertyData.ParkingType.ParkingDetail.ParkingType }}
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item class="flex justify-content-between">
                                <v-list-item-content>Parking Count:</v-list-item-content>
                                <v-list-item-content class="align-end">{{
                                    locationData.ResidentialPropertyData.ParkingType.ParkingDetail.ParkingCount }}
                                </v-list-item-content>
                            </v-list-item>

                            <v-list-item class="flex justify-content-between">
                                <v-list-item-content>Pool:</v-list-item-content>
                                <v-list-item-content class="align-end">{{
                                    locationData.ResidentialPropertyData.Pool }}
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item class="flex justify-content-between">
                                <v-list-item-content>Cooling:</v-list-item-content>
                                <v-list-item-content class="align-end">{{
                                    locationData.ResidentialPropertyData.Cooling }}
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item class="flex justify-content-between">
                                <v-list-item-content>Heating:</v-list-item-content>
                                <v-list-item-content class="align-end">{{
                                    locationData.ResidentialPropertyData.Heating }}
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item class="flex justify-content-between">
                                <v-list-item-content>Exterior Walls:</v-list-item-content>
                                <v-list-item-content class="align-end">{{
                                    locationData.ResidentialPropertyData.ExteriorWalls }}
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item class="flex justify-content-between">
                                <v-list-item-content>Number Of Patios:</v-list-item-content>
                                <v-list-item-content class="align-end">{{
                                    locationData.ResidentialPropertyData.NumberOfPatios }}
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item class="flex justify-content-between">
                                <v-list-item-content>Patio Type:</v-list-item-content>
                                <v-list-item-content class="align-end">{{
                                    locationData.ResidentialPropertyData.PatioType }}
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item class="flex justify-content-between">
                                <v-list-item-content>Physical Condition:</v-list-item-content>
                                <v-list-item-content class="align-end">{{
                                    locationData.ResidentialPropertyData.PhysicalCondition }}
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item class="flex justify-content-between">
                                <v-list-item-content>Roof Type:</v-list-item-content>
                                <v-list-item-content class="align-end">{{
                                    locationData.ResidentialPropertyData.RoofType }}
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item class="flex justify-content-between">
                                <v-list-item-content>Other Structures:</v-list-item-content>
                                <v-list-item-content class="align-end">{{
                                    locationData.ResidentialPropertyData.OtherStructures }}
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item class="flex justify-content-between">
                                <v-list-item-content>Locational Characteristics:</v-list-item-content>
                                <v-list-item-content class="align-end">{{
                                    locationData.ResidentialPropertyData.LocationalCharacteristics }}
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item class="flex justify-content-between">
                                <v-list-item-content>Original Construction Year:</v-list-item-content>
                                <v-list-item-content class="align-end">{{
                                    locationData.ResidentialPropertyData.OriginalConstructionYear }}
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item class="flex justify-content-between">
                                <v-list-item-content>Locational Factors:</v-list-item-content>
                                <v-list-item-content class="align-end">{{
                                    locationData.ResidentialPropertyData.LocationalFactors }}
                                </v-list-item-content>
                            </v-list-item>

                        </v-list>
                    </div>

                </v-card-text>
                <v-card-actions>

                </v-card-actions>
            </v-card>

            <v-card v-if="show.similarParcels">
                <v-card-title>Similar Parcels</v-card-title>
                <v-card-text>

                    <v-data-iterator
                            :items="locationData.SimilarParcels"
                            hide-default-footer
                    >
                        <template v-slot:default="props">
                            <v-row>
                                <v-col
                                        v-for="item in props.items"
                                        :key="item.name"
                                        cols="12"
                                        sm="6"
                                        md="4"
                                        lg="3"
                                >
                                    <v-card>
                                        <v-card-title class="subheading font-weight-bold">
                                            <a :href="'https://mcassessor.maricopa.gov/mcs.php?q=' + item.Parcel">{{
                                                item.Parcel }}</a>
                                        </v-card-title>

                                        <v-divider></v-divider>

                                        <v-list dense>
                                            <v-list-item>
                                                <v-list-item-content>Parcel:</v-list-item-content>
                                                <v-list-item-content class="align-end">{{ item.Parcel }}
                                                </v-list-item-content>
                                            </v-list-item>

                                            <v-list-item>
                                                <v-list-item-content>Land Size:</v-list-item-content>
                                                <v-list-item-content class="align-end">{{ item.LandSize }}
                                                </v-list-item-content>
                                            </v-list-item>

                                            <v-list-item>
                                                <v-list-item-content>Sales Price Date:</v-list-item-content>
                                                <v-list-item-content class="align-end">{{ item.SalesPriceDate }}
                                                </v-list-item-content>
                                            </v-list-item>

                                            <v-list-item>
                                                <v-list-item-content>Full Cash Value:</v-list-item-content>
                                                <v-list-item-content class="align-end">{{ item.FullCashValue }}
                                                </v-list-item-content>
                                            </v-list-item>

                                            <v-list-item>
                                                <v-list-item-content>Livable Sq Footage:</v-list-item-content>
                                                <v-list-item-content class="align-end">{{ item.LivableSqFootage }}
                                                </v-list-item-content>
                                            </v-list-item>

                                            <v-list-item>
                                                <v-list-item-content>Construction Year:</v-list-item-content>
                                                <v-list-item-content class="align-end">{{ item.ConstructionYear }}
                                                </v-list-item-content>
                                            </v-list-item>

                                            <v-list-item>
                                                <v-list-item-content>Pool Area:</v-list-item-content>
                                                <v-list-item-content class="align-end">{{ item.PoolArea }}
                                                </v-list-item-content>
                                            </v-list-item>

                                            <v-list-item>
                                                <v-list-item-content>Situs Address:</v-list-item-content>
                                                <v-list-item-content class="align-end">{{ item.SitusAddress }}
                                                </v-list-item-content>
                                            </v-list-item>

                                            <v-list-item>
                                                <v-list-item-content>Fore Closed:</v-list-item-content>
                                                <v-list-item-content class="align-end">{{ item.ForeClosed }}
                                                </v-list-item-content>
                                            </v-list-item>


                                        </v-list>
                                    </v-card>
                                </v-col>
                            </v-row>
                        </template>
                    </v-data-iterator>

                </v-card-text>
                <v-card-actions>

                </v-card-actions>
            </v-card>

            <v-card v-if="show.owner && dataReturned">
                <v-card-title>Owner Information</v-card-title>
                <v-card-text>
                    <div>

                        <v-divider></v-divider>

                        <v-list dense>

                            <v-list-item class="flex justify-content-between">
                                <v-list-item-content>Owner Name:</v-list-item-content>
                                <v-list-item-content class="align-end">{{
                                    locationData.Owner.OwnerName }}
                                </v-list-item-content>
                            </v-list-item>

                            <v-list-item class="flex justify-content-between">
                                <v-list-item-content>Full Mailing Address:</v-list-item-content>
                                <v-list-item-content class="align-end">{{
                                    locationData.Owner.FullMailingAddress }}
                                </v-list-item-content>
                            </v-list-item>

                            <v-list-item class="flex justify-content-between">
                                <v-list-item-content>Sale Price:</v-list-item-content>
                                <v-list-item-content class="align-end">{{
                                    locationData.Owner.SalePrice }}
                                </v-list-item-content>
                            </v-list-item>

                            <v-list-item class="flex justify-content-between">
                                <v-list-item-content>Sale Date:</v-list-item-content>
                                <v-list-item-content class="align-end">{{
                                    locationData.Owner.SaleDate }}
                                </v-list-item-content>
                            </v-list-item>

                            <v-list-item class="flex justify-content-between">
                                <v-list-item-content>Deed Date:</v-list-item-content>
                                <v-list-item-content class="align-end">{{
                                    locationData.Owner.DeedDate }}
                                </v-list-item-content>
                            </v-list-item>


                        </v-list>
                    </div>

                </v-card-text>
                <v-card-actions>

                </v-card-actions>
            </v-card>

            <v-card v-if="show.propertyData">
                <v-card-title>General Data</v-card-title>
                <v-card-text>
                    <div>

                        <v-divider></v-divider>

                        <v-list dense>

                            <v-list-item class="flex justify-content-between">
                                <v-list-item-content>Associated Parcels:</v-list-item-content>
                                <v-list-item-content class="align-end">{{ locationData.AssociatedParcels }}
                                </v-list-item-content>
                            </v-list-item>

                            <v-list-item class="flex justify-content-between">
                                <v-list-item-content>Property Address:</v-list-item-content>
                                <v-list-item-content class="align-end">{{ locationData.PropertyAddress }}
                                </v-list-item-content>
                            </v-list-item>

                            <v-list-item class="flex justify-content-between">
                                <v-list-item-content>Property Description:</v-list-item-content>
                                <v-list-item-content class="align-end">{{ locationData.PropertyDescription }}
                                </v-list-item-content>
                            </v-list-item>

                            <v-list-item class="flex justify-content-between">
                                <v-list-item-content>Section Township Range:</v-list-item-content>
                                <v-list-item-content class="align-end">{{ locationData.SectionTownshipRange }}
                                </v-list-item-content>
                            </v-list-item>

                            <v-list-item class="flex justify-content-between">
                                <v-list-item-content>Subdivision Name:</v-list-item-content>
                                <v-list-item-content class="align-end">{{ locationData.SubdivisionName }}
                                </v-list-item-content>
                            </v-list-item>

                            <v-list-item class="flex justify-content-between">
                                <v-list-item-content>Number Of Parcels In Subdivision:</v-list-item-content>
                                <v-list-item-content class="align-end">{{ locationData.NumberOfParcelsInSubdivision }}
                                </v-list-item-content>
                            </v-list-item>

                            <v-list-item class="flex justify-content-between">
                                <v-list-item-content>Number Of Parcels In MCR:</v-list-item-content>
                                <v-list-item-content class="align-end">{{ locationData.NumberOfParcelsInMCR }}
                                </v-list-item-content>
                            </v-list-item>

                            <v-list-item class="flex justify-content-between">
                                <v-list-item-content>Number Of Parcels In STR:</v-list-item-content>
                                <v-list-item-content class="align-end">{{ locationData.NumberOfParcelsInSTR }}
                                </v-list-item-content>
                            </v-list-item>

                            <v-list-item class="flex justify-content-between">
                                <v-list-item-content>Lot:</v-list-item-content>
                                <v-list-item-content class="align-end">{{ locationData.Lot }}
                                </v-list-item-content>
                            </v-list-item>

                            <v-list-item class="flex justify-content-between">
                                <v-list-item-content>Elementary School District:</v-list-item-content>
                                <v-list-item-content class="align-end">{{ locationData.ElementarySchoolDistrict }}
                                </v-list-item-content>
                            </v-list-item>

                            <v-list-item class="flex justify-content-between">
                                <v-list-item-content>PEProperty Use Description:</v-list-item-content>
                                <v-list-item-content class="align-end">{{ locationData.PEPropertyUseDescription }}
                                </v-list-item-content>
                            </v-list-item>

                            <v-list-item class="flex justify-content-between">
                                <v-list-item-content>Lot Size:</v-list-item-content>
                                <v-list-item-content class="align-end">{{ locationData.LotSize }}
                                </v-list-item-content>
                            </v-list-item>

                            <v-list-item class="flex justify-content-between">
                                <v-list-item-content>Is Commercial:</v-list-item-content>
                                <v-list-item-content class="align-end">{{ locationData.IsCommercial }}
                                </v-list-item-content>
                            </v-list-item>

                            <v-list-item class="flex justify-content-between">
                                <a :href="locationData.DeedTransitionUrl">Deed Transition Url</a>
                            </v-list-item>

                            <v-list-item class="flex justify-content-between">
                                <a :href="locationData.McrTransitionUrl">Mcr Transition Url</a>
                            </v-list-item>

                            <v-list-item class="flex justify-content-between">
                                <a :href="locationData.TreasurersTransitionUrl">Treasurers Transition Url</a>
                            </v-list-item>

                            <v-list-item class="flex justify-content-between">
                                <v-list-item-content>Property Type:</v-list-item-content>
                                <v-list-item-content class="align-end">{{ locationData.PropertyType }}
                                </v-list-item-content>
                            </v-list-item>

                            <v-list-item class="flex justify-content-between">
                                <v-list-item-content>Is Rental:</v-list-item-content>
                                <v-list-item-content class="align-end">{{ locationData.IsRental }}
                                </v-list-item-content>
                            </v-list-item>

                        </v-list>
                    </div>

                </v-card-text>
                <v-card-actions>

                </v-card-actions>
            </v-card>

        </v-container>

        <!--        <h1>Assessor</h1>-->

        <!--        {{ $route.params.location }}-->

        <!--        <pre>{{ locationData }}</pre>-->
    </div>
</template>

<script>
    import {mapState} from "vuex";

    export default {
        name: 'Assessor',
        data() {
            return {
                locationData: {},
                show: {
                    details: false,
                    valuations: false,
                    propertyData: false,
                    similarParcels: false,
                    residentialPropertyData: false,
                    owner: true
                },
                overlay: true,
                dataReturned: false
            }
        },
        methods: {
            showSection(section) {
                this.hideAllSections();
                if (section === 'valuations') {
                    this.show.valuations = true;
                } else if (section === 'details') {
                    this.show.details = true;
                } else if (section === 'propertyData') {
                    this.show.propertyData = true;
                } else if (section === 'similarParcels') {
                    this.show.similarParcels = true;
                } else if (section === 'residentialPropertyData') {
                    this.show.residentialPropertyData = true;
                } else if (section === 'owner') {
                    this.show.owner = true;
                }
            },
            hideAllSections() {
                this.show.details = false;
                this.show.valuations = false;
                this.show.propertyData = false;
                this.show.similarParcels = false;
                this.show.residentialPropertyData = false;
                this.show.owner = false;
            },
            parcel(parcel) {
                let parcelArray = parcel.split('-');
                return parcelArray[0] + parcelArray[1] + parcelArray[2];
            },
            setPage() {
                this.$store.commit('setCurrentPage', '/assessor');
            },
            async getAssessorData() {
                const {data} = await axios.get('/assessor/' + this.$route.params.location);
                if (data.error) {} else {
                    this.locationData = data;
                    this.dataReturned = true;
                }
                this.overlay = false;
            }
        },
        mounted() {
            this.setPage();
            this.overlay = true;
            this.getAssessorData();
        }
    }
</script>

<style scoped>

</style>