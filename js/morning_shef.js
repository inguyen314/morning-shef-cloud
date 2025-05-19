document.addEventListener('DOMContentLoaded', async function () {
    console.log('datetime: ', datetime);

    let setBaseUrl = null;
    if (cda === "internal") {
        setBaseUrl = `https://wm.${office.toLowerCase()}.ds.usace.army.mil/${office.toLowerCase()}-data/`;
    } else if (cda === "internal-coop") {
        setBaseUrl = `https://wm-${office.toLowerCase()}coop.mvk.ds.usace.army.mil/${office.toLowerCase()}-data/`;
    } else if (cda === "public") {
        setBaseUrl = `https://cwms-data.usace.army.mil/cwms-data/`;
    }
    console.log("setBaseUrl: ", setBaseUrl);

    const [month, day, year] = datetime.split('-');

    // Generate ISO strings for the previous 7 days and today
    const isoDateMinus8Days = getIsoDateWithOffsetDynamic(year, month, day, -8);
    const isoDateMinus7Days = getIsoDateWithOffsetDynamic(year, month, day, -7);
    const isoDateMinus6Days = getIsoDateWithOffsetDynamic(year, month, day, -6);
    const isoDateMinus5Days = getIsoDateWithOffsetDynamic(year, month, day, -5);
    const isoDateMinus4Days = getIsoDateWithOffsetDynamic(year, month, day, -4);
    const isoDateMinus3Days = getIsoDateWithOffsetDynamic(year, month, day, -3);
    const isoDateMinus2Days = getIsoDateWithOffsetDynamic(year, month, day, -2);
    const isoDateMinus1Day = getIsoDateWithOffsetDynamic(year, month, day, -1);
    const isoDateToday = getIsoDateWithOffsetDynamic(year, month, day, 0);

    // Generate ISO strings for the next 7 days
    const isoDateDay1 = getIsoDateWithOffsetDynamic(year, month, day, 1);
    const isoDateDay2 = getIsoDateWithOffsetDynamic(year, month, day, 2);
    const isoDateDay3 = getIsoDateWithOffsetDynamic(year, month, day, 3);
    const isoDateDay4 = getIsoDateWithOffsetDynamic(year, month, day, 4);
    const isoDateDay5 = getIsoDateWithOffsetDynamic(year, month, day, 5);
    const isoDateDay6 = getIsoDateWithOffsetDynamic(year, month, day, 6);
    const isoDateDay7 = getIsoDateWithOffsetDynamic(year, month, day, 7);

    console.log("isoDateMinus8Days:", isoDateMinus8Days);
    console.log("isoDateMinus7Days:", isoDateMinus7Days);
    console.log("isoDateMinus6Days:", isoDateMinus6Days);
    console.log("isoDateMinus5Days:", isoDateMinus5Days);
    console.log("isoDateMinus4Days:", isoDateMinus4Days);
    console.log("isoDateMinus3Days:", isoDateMinus3Days);
    console.log("isoDateMinus2Days:", isoDateMinus2Days);
    console.log("isoDateMinus1Day:", isoDateMinus1Day);
    console.log("isoDateToday:", isoDateToday);
    console.log("isoDateDay1:", isoDateDay1);
    console.log("isoDateDay2:", isoDateDay2);
    console.log("isoDateDay3:", isoDateDay3);
    console.log("isoDateDay4:", isoDateDay4);
    console.log("isoDateDay5:", isoDateDay5);
    console.log("isoDateDay6:", isoDateDay6);
    console.log("isoDateDay7:", isoDateDay7);

    const lakeLocs = [
        "Lk Shelbyville-Kaskaskia",
        "Carlyle Lk-Kaskaskia",
        "Rend Lk-Big Muddy",
        "Wappapello Lk-St Francis",
        "Mark Twain Lk-Salt"
    ];
    console.log("lakeLocs:", lakeLocs);

    const fetchTimeSeriesVersionedData = async (tsid) => {
        const tsidData = `${setBaseUrl}timeseries?name=${tsid}&begin=${isoDateMinus2Days}&end=${isoDateDay7}&office=${office}&version-date=${convertTo6AMCST(isoDateMinus1Day)}`; // use isoDateToday
        console.log('tsidData:', tsidData);
        try {
            const response = await fetch(tsidData, {
                headers: {
                    "Accept": "application/json;version=2",
                    "cache-control": "no-cache"
                }
            });
            const data = await response.json();
            return data;
        } catch (error) {
            console.error("Error fetching time series data:", error);
        }
    };

    const fetchTimeSeriesData = async (tsid) => {
        const tsidData = `${setBaseUrl}timeseries?name=${tsid}&begin=${isoDateToday}&end=${isoDateDay1}&office=${office}`; // use isoDateToday
        console.log('tsidData:', tsidData);
        try {
            const response = await fetch(tsidData, {
                headers: {
                    "Accept": "application/json;version=2",
                    "cache-control": "no-cache"
                }
            });
            const data = await response.json();
            return data;
        } catch (error) {
            console.error("Error fetching time series data:", error);
        }
    };

    const fetchAllLakeData = async (lakes) => {
        const allLakeData = [];

        for (const lake of lakes) {
            try {
                const urltsid1 = `${setBaseUrl}timeseries/group/Forecast-Lake?office=${office}&category-id=${lake}`;

                let urltsid2 = null;
                if (lake === "Lk Shelbyville-Kaskaskia") urltsid2 = `${setBaseUrl}timeseries/group/Flow?office=${office}&category-id=Shelbyville TW-Kaskaskia`;
                else if (lake === "Carlyle Lk-Kaskaskia") urltsid2 = `${setBaseUrl}timeseries/group/Flow?office=${office}&category-id=Carlyle-Kaskaskia`;
                else if (lake === "Rend Lk-Big Muddy") urltsid2 = `${setBaseUrl}timeseries/group/Flow?office=${office}&category-id=Rend Lk-Big Muddy`;
                else if (lake === "Wappapello Lk-St Francis") urltsid2 = `${setBaseUrl}timeseries/group/Flow?office=${office}&category-id=Iron Bridge-St Francis`;
                else if (lake === "Mark Twain Lk-Salt") urltsid2 = `${setBaseUrl}timeseries/group/Flow?office=${office}&category-id=Norton Bridge-Salt`;

                const [response1, response2] = await Promise.all([
                    fetch(urltsid1),
                    fetch(urltsid2)
                ]);

                const tsidData1 = await response1.json();
                const tsidData2 = await response2.json();

                const tsid1 = tsidData1['assigned-time-series'][0]['timeseries-id'];
                const tsid2 = tsidData2['assigned-time-series'][0]['timeseries-id'];

                const timeSeriesData1 = await fetchTimeSeriesVersionedData(tsid1);
                const timeSeriesData2 = await fetchTimeSeriesData(tsid2);

                allLakeData.push({
                    lake,
                    timeSeriesData1,
                    timeSeriesData2
                });
            } catch (error) {
                console.error(`Error fetching data for ${lake}:`, error);
            }
        }

        createTable(allLakeData); // pass array of lake data
    };

    fetchAllLakeData(lakeLocs);

    function convertTo6AMCST(isoDateToday) {
        // Parse the input date
        let date = new Date(isoDateToday);

        // Add 6 hours (6 * 60 * 60 * 1000 ms)
        date = new Date(date.getTime() + 6 * 60 * 60 * 1000);

        // Return the new ISO string
        return date.toISOString();
    }

    function convertUnixTimestamp(timestamp, toCST = false) {
        if (typeof timestamp !== "number") {
            console.error("Invalid timestamp:", timestamp);
            return null;
        }

        const dateUTC = new Date(timestamp);
        if (isNaN(dateUTC.getTime())) {
            console.error("Invalid date conversion:", timestamp);
            return null;
        }

        if (!toCST) {
            return dateUTC; // return as a real Date object
        }

        // Convert to CST/CDT Date object directly
        const options = {
            timeZone: "America/Chicago",
            year: "numeric", month: "2-digit", day: "2-digit",
            hour: "2-digit", minute: "2-digit", second: "2-digit",
            hour12: false
        };
        const parts = new Intl.DateTimeFormat("en-US", options).formatToParts(dateUTC);

        // Reconstruct local date string
        const get = (type) => parts.find(p => p.type === type)?.value || "00";
        const localDateString = `${get("year")}-${get("month")}-${get("day")}T${get("hour")}:${get("minute")}:${get("second")}`;
        return new Date(localDateString);
    }

    function createTable(lakeDataArray) {
        const outputDiv = document.getElementById("table_container");
        outputDiv.innerHTML = "";

        let allLakesText = "";

        lakeDataArray.forEach(({ lake, timeSeriesData1, timeSeriesData2 }) => {
            if (!timeSeriesData2 || !timeSeriesData2.values) {
                allLakesText += `<strong>${lake}</strong><br>No data available.<br><br>`;
                return;
            }

            const formattedForecastDataArray = timeSeriesData1.values.map(entry => {
                const timestamp = Number(entry[0]);
                return {
                    ...entry,
                    formattedTimestampCST: convertUnixTimestamp(timestamp, true),
                };
            });

            const formattedStageDataArray = timeSeriesData2.values.map(entry => {
                const timestamp = Number(entry[0]);
                return {
                    ...entry,
                    formattedTimestampCST: convertUnixTimestamp(timestamp, true),
                };
            });

            const sixAmData = formattedStageDataArray.find(entry => {
                const date = new Date(entry.formattedTimestampCST);
                return date.getHours() === 6 && date.getMinutes() === 0;
            });

            let nwsCode = null;
            if (lake === "Lk Shelbyville-Kaskaskia") nwsCode = "SBYI2";
            else if (lake === "Carlyle Lk-Kaskaskia") nwsCode = "CAYI2";
            else if (lake === "Rend Lk-Big Muddy") nwsCode = "RNDI2";
            else if (lake === "Wappapello Lk-St Francis") nwsCode = "WPPM7";
            else if (lake === "Mark Twain Lk-Salt") nwsCode = "CDAM7";

            let formattedDate = "NO_DATE";
            if (sixAmData) {
                const dateObj = new Date(sixAmData.formattedTimestampCST);
                const year = dateObj.getFullYear();
                const month = String(dateObj.getMonth() + 1).padStart(2, '0');
                const day = String(dateObj.getDate()).padStart(2, '0');
                formattedDate = `${year}${month}${day}`;
            }

            const forecastValues = formattedForecastDataArray.map(entry => {
                const rawValue = Number(entry[1]);
                const roundedValue = Math.round((rawValue / 1000) * 10) / 10;
                return roundedValue.toFixed(1);
            }).join(', ');

            const forecastText = `ER ${nwsCode} ${formattedDate} Z DH1200/QTIF/DID1/${forecastValues}`;

            if (sixAmData) {
                const value = (Math.round((Number(sixAmData[1]) / 1000) * 10) / 10).toFixed(2);

                allLakesText += `<strong>:${lake} (Today's lake flow 6am instantaneous value)</strong><br>.ER ${nwsCode} ${formattedDate} Z DH1200/QT/DID1/${value}<br>${forecastText}<br><br>`;
            } else {
                allLakesText += `<strong>:${lake}</strong><br>No 6 AM CST data available.<br><br>`;
            }
        });

        outputDiv.innerHTML = allLakesText;
    }
});

function formatISODateToCSTString(timestamp) {
    if (typeof timestamp !== "number") {
        console.error("Invalid timestamp:", timestamp);
        return "Invalid Date";
    }

    const date = new Date(timestamp); // Ensure timestamp is in milliseconds
    if (isNaN(date.getTime())) {
        console.error("Invalid date conversion:", timestamp);
        return "Invalid Date";
    }

    // Convert to CST (Central Standard Time)
    const options = {
        timeZone: 'America/Chicago',
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false,
    };

    const formatter = new Intl.DateTimeFormat('en-US', options);
    const formattedDate = formatter.format(date);

    return formattedDate.replace(',', ''); // Removes the comma between date and time
}

function getIsoDateWithOffsetDynamic(year, month, day, offset) {
    // Create a date object at 6 AM UTC
    const date = new Date(Date.UTC(year, month - 1, day, 6, 0, 0, 0));

    // Get the timezone offset dynamically based on CST/CDT
    const localTime = new Date(date.toLocaleString('en-US', { timeZone: 'America/Chicago' }));
    const timeOffset = (date.getTime() - localTime.getTime()) / (60 * 1000); // Offset in minutes

    // Adjust to 5 AM if not in daylight saving time
    if (localTime.getHours() !== 6) {
        date.setUTCHours(5);
    }

    // Adjust for the offset in days
    date.setUTCDate(date.getUTCDate() + offset);

    // Adjust for the timezone offset
    date.setMinutes(date.getMinutes() + timeOffset);

    // Return the ISO string
    return date.toISOString();
}

function getDSTOffsetInHours() {
    // Get the current date
    const now = new Date();

    // Get the current time zone offset in minutes (with DST, if applicable)
    const currentOffset = now.getTimezoneOffset();

    // Convert the offset from minutes to hours
    const dstOffsetHours = currentOffset / 60;

    return dstOffsetHours; // Returns the offset in hours (e.g., -5 or -6)
}