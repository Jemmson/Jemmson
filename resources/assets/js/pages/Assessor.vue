<template>
    <div>

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
                                            <a :href="'https://mcassessor.maricopa.gov/mcs.php?q=' + item.Parcel">{{ item.Parcel }}</a>
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

            <v-card v-if="show.owner">
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
            parcel(parcel){
                let parcelArray = parcel.split('-');
                return parcelArray[0] + parcelArray[1] + parcelArray[2];
            },
            setPage() {
                this.$store.commit('setCurrentPage', '/assessor');
            },
            async getAssessorData() {
                const {data} = await axios.get('/assessor/' + this.$route.params.location);

                console.log('data', data)

                if (data.error) {

                } else {
                    this.locationData = data;
                }
            }
        },
        created() {
            this.setPage();
            this.getAssessorData();
            // this.locationData = {
            //     "ParcelFound": true,
            //     "APN": {
            //         "Book": "301",
            //         "Map": "80",
            //         "Item": "258",
            //         "clean": "30180258",
            //         "formatted": "301-80-258",
            //         "link": "\/api\/parcel\/30180258"
            //     },
            //     "AssociatedParcels": null,
            //     "MoreAssociatedParcelsExist": false,
            //     "PropertyAddress": "705 E OXFORD DR    TEMPE 85283",
            //     "PropertyDescription": "UNIVERSITY ROYAL",
            //     "MCR": "15017",
            //     "SectionTownshipRange": "3 1S 4E",
            //     "SubdivisionName": "UNIVERSITY ROYAL",
            //     "SubdivisionID": "20879",
            //     "NumberOfParcelsInSubdivision": "409",
            //     "NumberOfParcelsInMCR": "409",
            //     "NumberOfParcelsInSTR": "0",
            //     "Lot": "254",
            //     "LocalJusidiction": "TEMPE",
            //     "ElementarySchoolDistrict": "TEMPE ELEMENTARY SCHOOL DISTRICT",
            //     "HighSchoolDistrict": "TEMPE UNION #213",
            //     "TaxAreaCode": "031600",
            //     "PropertyUseCode": "0131",
            //     "PEPropertyUseDescription": "Single Family Residence",
            //     "AssessorMarket": "02",
            //     "AreaNeighborhood": "007",
            //     "LotSize": "6504",
            //     "CityZone1": "R1-6",
            //     "IsCommercial": false,
            //     "Error": null,
            //     "DeedTransitionUrl": "https:\/\/recorder.maricopa.gov\/recdocdata\/GetRecDataDetailAssr.aspx?rec=20031679395&suf=&sar=Unofficial&docket=&docketpg=&docketdate=",
            //     "McrTransitionUrl": "https:\/\/recorder.maricopa.gov\/recdocdata\/internetmaps\/imapsdetail.aspx?Book=150&Page=17&imgType=TIF&maptype=PLAT",
            //     "TreasurersTransitionUrl": "https:\/\/treasurer.maricopa.gov\/parcel\/default.aspx?Parcel=30180258",
            //     "PropertyType": "Residential",
            //     "IsRental": false,
            //     "IsCancelled": false,
            //     "ResidentialPropertyData": {
            //         "AreaNeighboorhood": "007",
            //         "AssessorMarket": "02",
            //         "DetachedLivingArea": null,
            //         "MainLivingArea": "1267",
            //         "NumberOfCarports": "2",
            //         "NumberOfCoveredPatios": "0",
            //         "NumberOfGarages": null,
            //         "NumberofPatios": null,
            //         "NumberOfUncoveredPatios": "0",
            //         "ConstructionYear": "1973",
            //         "ImprovementQualityGrade": "R-3",
            //         "PEImprovementQualityGrade": "Average",
            //         "LivingSpace": "1267",
            //         "BathFixtures": "6",
            //         "CoveredParking": true,
            //         "ParkingType": {"ParkingDetail": {"ParkingType": "CP", "ParkingCount": "1"}},
            //         "Pool": false,
            //         "Cooling": "Refrigeration",
            //         "Heating": "Yes",
            //         "ExteriorWalls": "8\" Painted Block",
            //         "NumberOfPatios": "0",
            //         "PatioType": "None",
            //         "PhysicalCondition": null,
            //         "RoofType": "Asphalt Shingle",
            //         "OtherStructures": null,
            //         "LocationalCharacteristics": null,
            //         "OriginalConstructionYear": "1973",
            //         "LocationalFactors": null
            //     },
            //     "Improvements": null,
            //     "Valuations": [{
            //         "TaxYear": 2021,
            //         "FullCashValue": "215700",
            //         "LimitedPropertyValue": "141779",
            //         "LegalClassificationCode": "3.1",
            //         "LegalClassification": "PRIMARY RESIDENCE",
            //         "AssessmentRatioPercentage": 0.1,
            //         "AssessedFCV": null,
            //         "AssessedLPV": "14178",
            //         "ValuationSource": "Notice",
            //         "PEPropUseDesc": "Single Family Residence",
            //         "PropertyUseCode": "0131",
            //         "NoticeOfChangeIndicator": "N",
            //         "TaxAreaCode": "031600"
            //     }, {
            //         "TaxYear": 2020,
            //         "FullCashValue": "188700",
            //         "LimitedPropertyValue": "135028",
            //         "LegalClassificationCode": "3.1",
            //         "LegalClassification": "PRIMARY RESIDENCE",
            //         "AssessmentRatioPercentage": 0.1,
            //         "AssessedFCV": null,
            //         "AssessedLPV": "13503",
            //         "ValuationSource": "Notice",
            //         "PEPropUseDesc": "Single Family Residence",
            //         "PropertyUseCode": "0131",
            //         "NoticeOfChangeIndicator": "N",
            //         "TaxAreaCode": "031600"
            //     }, {
            //         "TaxYear": 2019,
            //         "FullCashValue": "175300",
            //         "LimitedPropertyValue": "128598",
            //         "LegalClassificationCode": "3.1",
            //         "LegalClassification": "PRIMARY RESIDENCE",
            //         "AssessmentRatioPercentage": 0.1,
            //         "AssessedFCV": null,
            //         "AssessedLPV": "12860",
            //         "ValuationSource": "Notice",
            //         "PEPropUseDesc": "Single Family Residence",
            //         "PropertyUseCode": "0131",
            //         "NoticeOfChangeIndicator": "N",
            //         "TaxAreaCode": "031600"
            //     }, {
            //         "TaxYear": 2018,
            //         "FullCashValue": "155100",
            //         "LimitedPropertyValue": "122474",
            //         "LegalClassificationCode": "3",
            //         "LegalClassification": "PRIMARY RESIDENCE",
            //         "AssessmentRatioPercentage": 0.1,
            //         "AssessedFCV": null,
            //         "AssessedLPV": "12247",
            //         "ValuationSource": "Notice",
            //         "PEPropUseDesc": "Single Family Residence",
            //         "PropertyUseCode": "0131",
            //         "NoticeOfChangeIndicator": "N",
            //         "TaxAreaCode": "031600"
            //     }, {
            //         "TaxYear": 2017,
            //         "FullCashValue": "138700",
            //         "LimitedPropertyValue": "116642",
            //         "LegalClassificationCode": "3",
            //         "LegalClassification": "PRIMARY RESIDENCE",
            //         "AssessmentRatioPercentage": 0.1,
            //         "AssessedFCV": null,
            //         "AssessedLPV": "11664",
            //         "ValuationSource": "Notice",
            //         "PEPropUseDesc": "Single Family Residence",
            //         "PropertyUseCode": "0131",
            //         "NoticeOfChangeIndicator": "N",
            //         "TaxAreaCode": "031600"
            //     }],
            //     "Owner": {
            //         "OwnerID": "2664055",
            //         "OwnerName": "BATTAFARANO KRISTEN",
            //         "OwnerMailAddress1": "705 E OXFORD DR",
            //         "OwnerMailAddress2": null,
            //         "OwnerCity": "TEMPE",
            //         "OwnerState": "AZ",
            //         "OwnerZip": "85283",
            //         "OwnerCountry": null,
            //         "OwnerStateResidCode": null,
            //         "FullMailingAddress": "705 E OXFORD DR, TEMPE, AZ 85283",
            //         "ContoOwner": null,
            //         "OwnerInCareOfName": null,
            //         "InCareOf": null,
            //         "SalePrice": "133000",
            //         "MostCurrentDeed": "031679395",
            //         "DeedType": "WD",
            //         "DeedDate": "12\/11\/2003",
            //         "SaleDate": "12\/01\/2003",
            //         "DeedTypeID": null
            //     },
            //     "RentalInformation": null,
            //     "SimilarParcels": [{
            //         "Parcel": "301-80-006",
            //         "LandSize": "6508",
            //         "SalesPriceDate": "$275,000\/07-2019",
            //         "FullCashValue": "215600",
            //         "LivableSqFootage": "1267",
            //         "ConstructionYear": "1973",
            //         "PoolArea": null,
            //         "SitusAddress": "207 E DUKE DR TEMPE, AZ 85283",
            //         "ForeClosed": "W1"
            //     }, {
            //         "Parcel": "301-80-032",
            //         "LandSize": "6656",
            //         "SalesPriceDate": "$284,000\/01-2019",
            //         "FullCashValue": "219700",
            //         "LivableSqFootage": "1267",
            //         "ConstructionYear": "1971",
            //         "PoolArea": null,
            //         "SitusAddress": "226 E COLGATE DR TEMPE, AZ 85283",
            //         "ForeClosed": "W1"
            //     }, {
            //         "Parcel": "301-80-034",
            //         "LandSize": "6062",
            //         "SalesPriceDate": "$260,000\/08-2018",
            //         "FullCashValue": "215500",
            //         "LivableSqFootage": "1267",
            //         "ConstructionYear": "1971",
            //         "PoolArea": null,
            //         "SitusAddress": "214 E COLGATE DR TEMPE, AZ 85283",
            //         "ForeClosed": "X"
            //     }, {
            //         "Parcel": "301-80-041",
            //         "LandSize": "6961",
            //         "SalesPriceDate": "$169,000\/03-2016",
            //         "FullCashValue": "219600",
            //         "LivableSqFootage": "1267",
            //         "ConstructionYear": "1971",
            //         "PoolArea": null,
            //         "SitusAddress": "103 E COLGATE DR TEMPE, AZ 85283",
            //         "ForeClosed": "W1"
            //     }, {
            //         "Parcel": "301-80-062",
            //         "LandSize": "6656",
            //         "SalesPriceDate": "$235,000\/05-2018",
            //         "FullCashValue": "228300",
            //         "LivableSqFootage": "1267",
            //         "ConstructionYear": "1972",
            //         "PoolArea": "356",
            //         "SitusAddress": "208 E OXFORD DR TEMPE, AZ 85283",
            //         "ForeClosed": "X"
            //     }]
            // }
        }
    }
</script>

<style scoped>

</style>