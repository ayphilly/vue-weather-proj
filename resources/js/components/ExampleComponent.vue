<template>
    <div class="text-white mb-8 flex-column mx-auto">
        <div class="places-input text-gray-500 w-500" >
            <input type="search" id="address" class="form-control" placeholder="Where are we going?" />
            <p>Selected: <strong id="address-value">none</strong></p>
        </div>        
        <div class="weather-container font-sans w-150 max-w-lg overflow-hidden bg-gray-900 shadow-lg mt-4 rounded-lg mx-auto">
            <div class="current-weather flex items-center justify-between px-6 py-8 bg-gray-800 ">
                <div class="flex items-center justify-center">
                    <div>
                        <div class="text-6xl font-semibold ">{{ currenTemperature.actual }}°C  </div>
                        <div> Feels like {{ currenTemperature.feels }}°C</div>
                    </div>
                    <div class="ml-4">
                        <p class="font-semibold">{{currenTemperature.summary}}</p>
                        <p>  {{location.area }}, {{ location.region}}</p>
                    </div>
                    
                </div>
                <div>
                    <canvas id="cloudy" width="96" height="96"></canvas>                
                </div>
            </div> <!-- end weather -->

            <div class="future-weather text-sm bg-color-gray-700 px-6 py-8 overflow-hidden">              

                <div v-for=" (days , index) in daily " :key = "days.date_epoch" class="flex items-center content-center " :class="{ 'mt-8' : index > 0 }" >
                    <div class="w-1/6 text-lg text-gray-500">{{ toDayOfTheWeek(days.date)}}</div>
                    <div class="w-4/6 px-4 flex items-center">
                        <div class="mr-5">
                            
                            <canvas :id="`icon${index+1}` " :data-icon="toKebab(days.day.condition.text)"  width="24" height="24"></canvas> 
                        </div>
                        <div>{{ days.day.condition.text}} </div>
                    </div>
                    <div class="w-1/6 text-right">
                        <div> {{ Math.round(days.day.maxtemp_c )}} °C </div>
                        <div> {{ Math.round(days.day.mintemp_c )}} °C </div>
                    </div>
                </div>

            </div>

        </div> <!-- end weather container -->
        
    </div>
</template>

<script>
    export default {
        mounted() {
            this.fetchData();
            const placesAutocomplete = places({
                appId: 'plC6C3B3IDNA',
                apiKey: 'a634f0393dd7c8e7a3bf831ac28acdd0',
                container: document.querySelector('#address')
            }).configure({
                type: 'city',
                aroundLatLngViaIP : false
            });
            var $address = document.querySelector('#address-value')
            placesAutocomplete.on('change', (e) => {
                $address.textContent = e.suggestion.value; 
                this.location.area = e.suggestion.name;
                this.location.region = e.suggestion.country;
                this.location.lat = e.suggestion.latlng.lat;
                this.location.lon = e.suggestion.latlng.lng;
            });

            placesAutocomplete.on('clear', function() {
                $address.textContent = 'none';
            });
        },
        watch : {
            location : {
                handler(newValue, oldValue) {
                    this.fetchData()
                },
                deep: true
            }
        },
        data() {
            return {
                currenTemperature : {
                    actual: '',
                    feels : '',
                    summary: '',
                    icon: ''
                },
                daily : [],
                location : {
                    area : "london",
                    region : '',
                    lat : '',
                    lon : ''
                }
            }
        },
        methods: {
            fetchData ( ) {
                fetch(`http://api.weatherapi.com/v1/forecast.json?key=bb08fb876b9443f9a89214133200412&q=${this.location.area}&days=5`)  
                .then (response => response.json())              
                .then (data => {
                    var icons = new Skycons({'color': 'white'});
                    icons.play();
                    // console.log(data)
                    this.location.lat = data.location.lat;
                    this.location.lon = data.location.lon;
                    this.location.region = data.location.region;
                    this.currenTemperature.actual = Math.round(data.current.temp_c);                   
                    this.currenTemperature.feels = Math.round(data.current.feelslike_c);
                    this.currenTemperature.summary = data.current.condition.text;
                    this.currenTemperature.icon = data.current.condition.icon;
                    
                    
                    
                    
                    icons.add("cloudy", this.toKebab(this.currenTemperature.summary));
                    icons.add("icon3", icons.RAINY);
                    
                    this.daily = data.forecast.forecastday;
                    this.$nextTick (()=> {
                        icons.add("icon1", document.getElementById("icon1").getAttribute('data-icon')+"-day");
                        icons.add("icon2", document.getElementById("icon2").getAttribute('data-icon')+"-day");
                        // icons.add("icon3", document.getElementById("icon3").getAttribute('data-icon'));
                        icons.play();

                    }) 
                    console.log(data);


                    
                })

            },
            toKebab (stringToConvert) {
                return stringToConvert.split(' ').join('-');
            },
            toDayOfTheWeek (timeStamp) {
                const date = new Date(timeStamp);
                const days = ['SUN', 'MON', 'TUE', 'WED', 'THURS', 'FRI', 'SAT']
                return days[date.getDay()];
            }
        }
    }
</script>
