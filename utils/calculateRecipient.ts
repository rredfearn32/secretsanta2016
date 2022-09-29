import { User } from './types';

const randomIntFromInterval = (min: number, max: number) =>
  Math.floor(Math.random() * (max - min + 1) + min);

export const calculateRecipient = (users: User[], currentUser: User): User => {
  const { name: currentUserName } = currentUser;

  const availableUsers = currentUser.possibleChoices
    .map((name) => users.find((user) => user.name === name))
    .filter(Boolean);
  const chosenNames = users
    .map((user) => user.chosen && user.name)
    .filter(Boolean);
  const peopleWhoHaveMadeTheirChoice = users
    .map((user) => user.choiceMade && user.name)
    .filter(Boolean);

  if (peopleWhoHaveMadeTheirChoice.includes(currentUserName)) return;

  const dictionaryOfPopularity: { [key: string]: number } = {};

  availableUsers.forEach(({ name }) => {
    if (!chosenNames.includes(name)) {
      const nameOfPersonForPopularity = name;
      let popularity = 0;

      availableUsers.forEach(({ name, possibleChoices }) => {
        if (!peopleWhoHaveMadeTheirChoice.includes(name)) {
          if (possibleChoices.includes(nameOfPersonForPopularity)) {
            popularity++;
          }
        }
      });

      dictionaryOfPopularity[nameOfPersonForPopularity] = popularity;
    }
  });

  console.log(dictionaryOfPopularity);

  const minPop = Object.entries(dictionaryOfPopularity).sort(
    ([, popA], [, popB]) => popA - popB,
  )[0][1];
  const lowestPopularityNames = [];

  Object.entries(dictionaryOfPopularity).forEach(([key, value]) => {
    if (value === minPop) {
      lowestPopularityNames.push(key);
    }
  });

  const lengthOfLowPopNamesArray = lowestPopularityNames.length;
  const randomNumber = randomIntFromInterval(0, lengthOfLowPopNamesArray - 1);

  const result = lowestPopularityNames[randomNumber];

  return users.find(({ name }) => name === result);
};
