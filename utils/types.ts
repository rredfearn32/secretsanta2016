export type User = {
  name: string;
  choiceMade: boolean;
  possibleChoices: string[];
  chosen: boolean;
  chosenPerson: string | undefined;
};
