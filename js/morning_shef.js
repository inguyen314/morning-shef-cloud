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
        const tsidData = `${setBaseUrl}timeseries?name=${tsid}&begin=${isoDateMinus2Days}&end=${isoDateDay7}&office=${office}&version-date=${convertTo6AMCST(isoDateToday)}`; // use isoDateToday
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

    const fetchTimeSeriesTurbData = async (tsid) => {
        // Convert to Date object
        const date = new Date(isoDateMinus1Day);

        // Add 1 hour (60 minutes * 60 seconds * 1000 milliseconds)
        date.setTime(date.getTime() + (1 * 60 * 60 * 1000));

        // Convert back to ISO string (preserve UTC format)
        const begin = date.toISOString();

        const tsidData = `${setBaseUrl}timeseries?name=${tsid}&begin=${begin}&end=${isoDateToday}&office=${office}`; // use isoDateToday
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

                const urltsid3 = (lake === "Mark Twain Lk-Salt")
                    ? `${setBaseUrl}timeseries/group/Turbines-Lake-Test?office=${office}&category-id=${lake}`
                    : null;

                const fetchPromises = [fetch(urltsid1), fetch(urltsid2)];
                if (urltsid3) fetchPromises.push(fetch(urltsid3));

                const responses = await Promise.all(fetchPromises);

                const tsidData1 = await responses[0].json();
                const tsidData2 = await responses[1].json();
                const tsidData3 = urltsid3 ? await responses[2].json() : null;

                const tsid1 = tsidData1['assigned-time-series'][0]['timeseries-id'];
                const tsid2 = tsidData2['assigned-time-series'][0]['timeseries-id'];
                const tsid3 = tsidData3 ? tsidData3['assigned-time-series'][0]['timeseries-id'] : null;

                const timeSeriesData1 = await fetchTimeSeriesVersionedData(tsid1);
                const timeSeriesData2 = await fetchTimeSeriesData(tsid2);
                const timeSeriesData3 = tsid3 ? await fetchTimeSeriesTurbData(tsid3) : null;

                allLakeData.push({
                    lake,
                    timeSeriesData1,
                    timeSeriesData2,
                    timeSeriesData3
                });
            } catch (error) {
                console.error(`Error fetching data for ${lake}:`, error);
            }
        }

        createTable(allLakeData);
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
        const savePromises = [];

        let displayText = "";
        let plainText = "";

        lakeDataArray.forEach(({ lake, timeSeriesData1, timeSeriesData2, timeSeriesData3 }) => {
            if (!timeSeriesData2 || !timeSeriesData2.values) {
                allLakesText += `<strong>${lake}</strong><br>No data available.<br><br>`;
                return;
            }

            console.log("timeSeriesData3:", timeSeriesData3);

            const formattedForecastDataArray = timeSeriesData1.values.map(entry => {
                const timestamp = Number(entry[0]);
                return {
                    ...entry,
                    formattedTimestampCST: convertUnixTimestamp(timestamp, true),
                };
            });
            console.log("formattedForecastDataArray:", formattedForecastDataArray);

            const formattedFlowDataArray = timeSeriesData2.values.map(entry => {
                const timestamp = Number(entry[0]);
                return {
                    ...entry,
                    formattedTimestampCST: convertUnixTimestamp(timestamp, true),
                };
            });

            const sixAmFlowData = formattedFlowDataArray.find(entry => {
                const date = new Date(entry.formattedTimestampCST);
                return date.getHours() === 6 && date.getMinutes() === 0;
            });

            let nwsCode = null;
            if (lake === "Lk Shelbyville-Kaskaskia") nwsCode = "SBYI2";
            else if (lake === "Carlyle Lk-Kaskaskia") nwsCode = "CAYI2";
            else if (lake === "Rend Lk-Big Muddy") nwsCode = "RNDI2";
            else if (lake === "Wappapello Lk-St Francis") nwsCode = "WPPM7";
            else if (lake === "Mark Twain Lk-Salt") nwsCode = "CDAM7";

            let formattedFlowDate = "NO_DATE";
            if (sixAmFlowData) {
                const dateObj = new Date(sixAmFlowData.formattedTimestampCST);
                const year = dateObj.getFullYear();
                const month = String(dateObj.getMonth() + 1).padStart(2, '0');
                const day = String(dateObj.getDate()).padStart(2, '0');
                formattedFlowDate = `${year}${month}${day}`;
            }

            const forecastValues = formattedForecastDataArray.map(entry => {
                const rawValue = Number(entry[1]);
                const roundedValue = Math.round((rawValue / 1000) * 10) / 10;
                return roundedValue.toFixed(1);
            }).join(', ');

            let forecastText = "";
            if (forecastValues) {
                forecastText = `.ER ${nwsCode} ${formattedFlowDate} Z DH1200/QTIF/DID1/${forecastValues}`;
            } else {
                forecastText = `.ER ${nwsCode} ${formattedFlowDate} Z DH1200/QTIF/DID1/No data available`;
            }

            const flowYesterdayTurbine = (
                timeSeriesData3 &&
                Array.isArray(timeSeriesData3.values) &&
                timeSeriesData3.values.length > 0 &&
                Array.isArray(timeSeriesData3.values[0]) &&
                timeSeriesData3.values[0].length > 1
            ) ? timeSeriesData3.values[0][1] : null;

            console.log("flowYesterdayTurbine:", flowYesterdayTurbine);

            let turbineText = "";
            if (lake === "Mark Twain Lk-Salt") {
                if (flowYesterdayTurbine !== null) {
                    turbineText = `.ER ${nwsCode} ${formattedFlowDate} Z DH1200/QTD/DID1/${flowYesterdayTurbine}`;
                } else {
                    turbineText = `.ER ${nwsCode} ${formattedFlowDate} Z DH1200/QTD/DID1/No data available`;
                }
            }

            if (sixAmFlowData) {
                const value = (Math.round((Number(sixAmFlowData[1]) / 1000) * 10) / 10).toFixed(2);

                plainText += `:${lake} (Today's lake flow 6am instantaneous value)\n.ER ${nwsCode} ${formattedFlowDate} Z DH1200/QT/DID1/${value}\n${forecastText}\n${turbineText}\n\n`;

                displayText += `<strong>:${lake} (Today's lake flow 6am instantaneous value)</strong><br>.ER ${nwsCode} ${formattedFlowDate} Z DH1200/QT/DID1/<span title='${timeSeriesData2['name']}'>${value}</span><br>${forecastText}<br>${turbineText}<br><br>`;
            } else {
                displayText += `<strong>:${lake}</strong><br>No 6 AM CST data available.<br><br>`;
            }

            // Save to BLOB and show feedback
            // NOTE: https://cwms-data.usace.army.mil/cwms-data/blobs/MORNING_SHEF.TXT?office=MVS
            // curl -O https://cwms-data.usace.army.mil/cwms-data/blobs/MORNING_SHEF.TXT?office=MVS
            const savePromise = fetch(`${setBaseUrl.replace(":8243", "")}blobs?fail-if-exists=false`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json;version=2",
                    "cache-control": "no-cache",
                },
                body: JSON.stringify({
                    "office-id": office,
                    "media-type-id": "application/octet-stream",
                    "id": "MORNING_SHEF.TXT",
                    "description": `Updated ${moment().format()}`,
                    "value": btoa(plainText),
                }),
            })
                .then(response => {
                    if (response.ok) {
                        outputDiv.innerHTML += `<div style="color: green;">Saved for ${lake}</div>`;
                    } else {
                        return response.text().then(text => {
                            throw new Error(`Save failed for ${lake}: ${response.status} - ${text}`);
                        });
                    }
                })
                .catch(error => {
                    console.error("Save error:", error);
                    outputDiv.innerHTML += `<div style="color: red;">Failed to save for ${lake}</div>`;
                });

            savePromises.push(savePromise);
        });

        // Append "Good job!" after all lakes are saved
        Promise.all(savePromises).finally(() => {
            outputDiv.innerHTML += `<div style="color: blue; margin-top: 10px;"><strong>https://cwms-data.usace.army.mil/cwms-data/blobs/MORNING_SHEF.TXT?office=MVS</strong></div>`;
        });

        outputDiv.innerHTML = displayText;
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