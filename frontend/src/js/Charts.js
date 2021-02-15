import 'chartjs-plugin-labels';
import {increaseMaxYaxes,stripNumber }  from './Helpers';
/**
 *
 * @param {RequestInfo} url
 * @param {RequestInit} params
 * @return {Promise<Object>}
 */
export function chartDoughnut (element,datas) {

  let chartDoughnut = new Chart(document.getElementById(element), {
    type: 'doughnut',
    data: {
      labels: datas.label,
      datasets: [
        {
          label: "Population (millions)",
          backgroundColor: datas.bgColor,
          data: datas.data
        }
      ]
    },
    options: {
      legend :{
        align: 'start',
        position: "top",
        fullWidth:true,
        labels:{
          boxWidth:20,
          padding:10
        }
      },
      title: {
        display: true,
        text: datas.titleTxt,
        fontSize:22,
        fontColor:"#990099"
      },
      tooltips: {
            callbacks: {
                label: function(tooltipItem, data) {
                  // console.log(data.labels)
                  // console.log(data)
                    return data.labels[tooltipItem.index]
                }
            }
      },
      plugins: {
        labels: {
          render: 'value',
          fontColor: 'white',
          precision: 2
        }
      },
    }
  });
	
}

export function chartPie (element,datas) {

  let chartPie = new Chart(document.getElementById(element), {
    type: 'pie',
    data: {
      labels: datas.label,
      datasets: [
        {
          label: "Population (millions)",
          backgroundColor: datas.bgColor,
          data: datas.data
        }
      ]
    },
    options: {
      title: {
        display: true,
        text:  datas.titleTxt,
        fontSize:22,
        fontColor:"#990099"
      },
      tooltips: {
            callbacks: {
                label: function(tooltipItem, data) {
                  // console.log(data.labels)
                  // console.log(data)
                    return data.labels[tooltipItem.index]
                }
            }
      },
      plugins: {
        labels: {
          render: 'value',
          fontColor: ['white', 'white','white'],
          precision: 2
        }
      },
      
      
    }
});
	
}

export async function processChartStandingPage(getStats){
  //chart 2 for goals by stage
 
  let dataChartGoalByStage = [0];
  dataChartGoalByStage.push(...await getStats.goalByStage)
  let maxGoal = Math.max(...dataChartGoalByStage)

  var chartGoalByStage = await document.getElementById('chart-goals-by-stage')
  let myChartaa = await new Chart(chartGoalByStage, {
    type: 'line',
    data: {
      labels: [ ...Array(31).keys() ].map( i => i),
      datasets: [{
        data: dataChartGoalByStage,
        lineTension: 0,
        backgroundColor: 'transparent',
        borderColor: '#990099',
        borderWidth: 3,
        pointBackgroundColor: '#990099',
        pointBorderWidth:0.2,
        pointRadius:4,
        pointRotation:180
      }]
    },
    options: {
      scales: {
        xAxes: [{
						display: true,
						scaleLabel: {
							display: true,
							labelString: 'Journey'
						}
				}],
        yAxes: [{
          ticks: {
            beginAtZero: false,
            reverse: false,
            min:1,
            stepSize: 5,
            max:maxGoal+5,
            callback: function(value, index, values) {
                        return '' + value;
                    }
          },
           scaleLabel: {
            display: true,
            labelString: 'Goals'
          }
        }]
      },
      legend: {
        display: false
      },
      tooltips: {
            callbacks: {
                title: function(tooltipItem, data) {
                   return 'Journey: ' + tooltipItem[0].xLabel;
                },
                label: function(tooltipItem, data) {
                    return 'Goals: ' + tooltipItem.yLabel 
                }
            }
      },
      title: {
					display: true,
          text: 'Goals by Stage',
          fontSize:22,
          fontColor:"#990099"
			},
    }
  })
  //chart 2 for  goals by minutes
  let goalMins = await getStats.goalMins
  let goalsByMinutes = [];
  goalsByMinutes.push(goalMins.mins1,goalMins.mins2,goalMins.mins3,goalMins.mins4,goalMins.mins5,goalMins.mins6);
  let maxGoalMinutes = Math.max(...goalsByMinutes)
    var chartGoalByMinutes = await document.getElementById('chart-goals-by-minutes')
  let myChartaaa = await new Chart(chartGoalByMinutes, {
    type: 'bar',
    data: {
      labels: ['0 - 15 min ','15 - 30 min ','30 - 45 min ','45 - 60 min ','60 - 75 min ','75 - 90++ min '],
      datasets: [{
        data: goalsByMinutes,
        lineTension: 0,
        backgroundColor: '#990099',
        borderColor: '#990099',
        borderWidth: 3,
        pointBackgroundColor: '#990099',
        pointBorderWidth:0.2,
        pointRadius:4,
        pointRotation:180
      }]
    },
    options: {
      scales: {
        xAxes: [{
						display: true,
						scaleLabel: {
							display: true,
							labelString: 'Minutes'
						}
				}],
        yAxes: [{
          ticks: {
            beginAtZero: false,
            reverse: false,
            min:1,
            stepSize: 10,
            max:maxGoalMinutes,
            callback: function(value, index, values) {
                        return '' + value;
                    }
          },
           scaleLabel: {
            display: true,
            labelString: 'Goals'
          }
        }]
      },
      legend: {
        display: false
      },
      tooltips: {
            callbacks: {
                title: function(tooltipItem, data) {
                   return 'Minutes: ' + tooltipItem[0].xLabel;
                },
                label: function(tooltipItem, data) {
                    return 'Goals: ' + tooltipItem.yLabel 
                }
            }
      },
      title: {
					display: true,
          text: 'Goals by Minutes'
          ,
          fontSize:22,
          fontColor:"#990099"
      },
      plugins: {
        labels: {
          render: 'value',
          fontColor: '#000'
        }
      },
    }
  })
}


export async function processChartTeamIdPage(stats){

  new Chart(document.getElementById("games-home-away-chart"), {
    type: 'bar',
    data: {
      labels: ["Home", "Away"],
      datasets: [
        {
          label: parseInt(stats.homeWins+stats.awayWins)+" Win",
          backgroundColor: "#3e95cd",
          data: [stats.homeWins,stats.awayWins]
        }, 
        {
          label: parseInt(stats.homeDraw+stats.awayDraw)+" Draw",
          backgroundColor: "#5a3ddb",
          data: [stats.homeDraw,stats.awayDraw]
        }, 
        {
          label: parseInt(stats.homeLos+stats.awayLos)+" Los",
          backgroundColor: "#8e5ea2",
          data: [stats.homeLos,stats.awayLos]
        }
      ]
    },
    options: {
      title: {
        display: true,
        text: 'Games Home/Away',
        fontSize:18,
        fontColor:"#990099"
      },
      scales: {
        xAxes: [{
            display: true,
            scaleLabel: {
              display: true,
              labelString: stats.name_short.toUpperCase()
            }
        }],
        yAxes: [{
          ticks: {
            beginAtZero: false,
            reverse: false,
            min:0,
            stepSize: 5,
            max:15
          },
          scaleLabel: {
            display: true,
            labelString: 'Goals'
          }
        }]
      },
      tooltips: {
        callbacks: {
            label: function(tooltipItem, data) {
              // console.log(data.labels)
              // console.log(data)
              // console.log(data.datasets[tooltipItem.index])
              let newLabel = data.datasets[tooltipItem.datasetIndex].label;
              newLabel = stripNumber(newLabel)+": "+tooltipItem.yLabel;
              // console.log(newLabel)
              // console.log(tooltipItem.yLabel)
              // console.log(tooltipItem)
              // return data.labels[tooltipItem.index]
              return newLabel
            }
        }
      },
      // "animation": {
      //   "duration": 1,
      //   "onComplete": function () {
      //     var chartInstance = this.chart,
      //       ctx = chartInstance.ctx;
          
      //     ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontSize, Chart.defaults.global.defaultFontStyle, Chart.defaults.global.defaultFontFamily);
      //     ctx.textAlign = 'center';
      //     ctx.textBaseline = 'bottom';

      //     this.data.datasets.forEach(function (dataset, i) {
      //       var meta = chartInstance.controller.getDatasetMeta(i);
      //       meta.data.forEach(function (bar, index) {
      //         var data = dataset.data[index];                            
      //         ctx.fillText(data, bar._model.x, bar._model.y - 5);
      //       });
      //     });
      //   }
      // },
       plugins: {
        labels: {
          render: 'value',
          fontColor: '#000'
        }
      },
    }
  });
  new Chart(document.getElementById("goals-bar-chart"), {
    type: 'bar',
    data: {
      labels: ["Home", "Away"],
      datasets: [
        {
          label: parseInt(stats.homeGoals+stats.awayGoals)+" Goals For",
          backgroundColor: "#3e95cd",
          data: [stats.homeGoals,stats.awayGoals]
        }, {
          label: parseInt(stats.homeGoalsAgainst+stats.awayGoalsAgainst)+" Goals Against",
          backgroundColor: "#8e5ea2",
          data: [stats.homeGoalsAgainst,stats.awayGoalsAgainst]
        }
      ]
    },
    options: {
      title: {
        display: true,
        text: 'Goals Home/Away',
        fontSize:18,
        fontColor:"#990099"
      },
      scales: {
        xAxes: [{
            display: true,
            scaleLabel: {
              display: true,
              labelString: stats.name_short.toUpperCase()
            }
        }],
        yAxes: [{
          ticks: {
            beginAtZero: false,
            reverse: false,
            min:0,
            stepSize: 5,
            max:increaseMaxYaxes([stats.homeGoals,stats.awayGoals,stats.homeGoalsAgainst,stats.awayGoalsAgainst])
          },
          scaleLabel: {
            display: true,
            labelString: 'Goals'
          }
        }]
      },
      tooltips: {
        callbacks: {
            label: function(tooltipItem, data) {
              // console.log(data.labels)
              // console.log(data)
              // console.log(data.datasets[tooltipItem.index])
              let newLabel = data.datasets[tooltipItem.datasetIndex].label;
              newLabel = stripNumber(newLabel)+": "+tooltipItem.yLabel;
              // console.log(newLabel)
              // console.log(tooltipItem.yLabel)
              // console.log(tooltipItem)
              // return data.labels[tooltipItem.index]
              return newLabel
            }
        }
      },
      plugins: {
        labels: {
          render: 'value',
          fontColor: '#000'
        }
      },
    }
  });


  let goalMins = stats.goalsByMinutes
  let goalsByMinutes = [];
  goalsByMinutes.push(goalMins.mins1,goalMins.mins2,goalMins.mins3,goalMins.mins4,goalMins.mins5,goalMins.mins6);
  let maxGoalMinutes = Math.max(...goalsByMinutes)

  new Chart(document.getElementById('chart-goals-by-minutes-team-page'), {
    type: 'bar',
    data: {
      labels: ['0 - 15 min ','15 - 30 min ','30 - 45 min ','45 - 60 min ','60 - 75 min ','75 - 90++ min '],
      datasets: [{
        data: goalsByMinutes,
        lineTension: 0,
        backgroundColor: '#3e95cd',
        borderColor: '#3e95cd',
        borderWidth: 3,
        pointBackgroundColor: '#3e95cd',
        pointBorderWidth:0.2,
        pointRadius:4,
        pointRotation:180
      }]
    },
    options: {
      scales: {
        xAxes: [{
						display: true,
						scaleLabel: {
							display: true,
							labelString: 'Minutes'
						}
				}],
        yAxes: [{
          ticks: {
            beginAtZero: true,
            reverse: false,
            min:0,
            stepSize: 5,
            max:maxGoalMinutes,
            callback: function(value, index, values) {
                        return '' + value;
                    }
          },
           scaleLabel: {
            display: true,
            labelString: 'Goals'
          }
        }]
      },
      legend: {
        display: false
      },
      tooltips: {
            callbacks: {
                title: function(tooltipItem, data) {
                   return 'Minutes: ' + tooltipItem[0].xLabel;
                },
                label: function(tooltipItem, data) {
                    return 'Goals: ' + tooltipItem.yLabel 
                }
            }
      },
      title: {
					display: true,
          text: 'Goals for '+stats.name_short.toUpperCase()+' by minutes'
          ,
          fontSize:22,
          fontColor:"#3e95cd"
      },
      plugins: {
        labels: {
          render: 'value',
          fontColor: '#000'
        }
      },
    }
  })



  let goalMinsAgaint = stats.goalsByMinutesAgainst
  let goalsByMinutesAgainst = [];
  goalsByMinutesAgainst.push(goalMinsAgaint.mins1,goalMinsAgaint.mins2,goalMinsAgaint.mins3,goalMinsAgaint.mins4,goalMinsAgaint.mins5,goalMinsAgaint.mins6);
  let maxGoalMinutesAgainst = Math.max(...goalsByMinutesAgainst)

  new Chart(document.getElementById('chart-goals-by-minutes-against-team-page'), {
    type: 'bar',
    data: {
      labels: ['0 - 15 min ','15 - 30 min ','30 - 45 min ','45 - 60 min ','60 - 75 min ','75 - 90++ min '],
      datasets: [{
        data: goalsByMinutesAgainst,
        lineTension: 0,
        backgroundColor: '#3e95cd',
        borderColor: '#3e95cd',
        borderWidth: 3,
        pointBackgroundColor: '#3e95cd',
        pointBorderWidth:0.2,
        pointRadius:4,
        pointRotation:180
      }]
    },
    options: {
      scales: {
        xAxes: [{
						display: true,
						scaleLabel: {
							display: true,
							labelString: 'Minutes'
						}
				}],
        yAxes: [{
          ticks: {
            beginAtZero: true,
            reverse: false,
            min:0,
            stepSize: 5,
            max:maxGoalMinutesAgainst,
            callback: function(value, index, values) {
                        return '' + value;
                    }
          },
           scaleLabel: {
            display: true,
            labelString: 'Goals'
          }
        }]
      },
      legend: {
        display: false
      },
      tooltips: {
            callbacks: {
                title: function(tooltipItem, data) {
                   return 'Minutes: ' + tooltipItem[0].xLabel;
                },
                label: function(tooltipItem, data) {
                    return 'Goals: ' + tooltipItem.yLabel 
                }
            }
      },
      title: {
					display: true,
          text: 'Goals against '+stats.name_short.toUpperCase()+' by minutes',
          fontSize:22,
          fontColor:"#3e95cd"
      },
      plugins: {
        labels: {
          render: 'value',
          fontColor: '#000'
        }
      },
    }
  })


}