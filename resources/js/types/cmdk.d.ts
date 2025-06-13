declare module 'cmdk' {
  import * as React from 'react';

  // Basistyp für alle Einzelelemente des Command-Paletten-Kits
  // (Props sind sehr generisch – für präzisere Typen bitte die originalen
  //  *.d.ts-Dateien aus dem Paket importieren.)
  export interface CommandBaseProps extends React.HTMLAttributes<HTMLElement> {
    loop?: boolean;
  }

  export const Command: React.FC<CommandBaseProps>;
  export const CommandInput: React.FC<React.InputHTMLAttributes<HTMLInputElement>>;
  export const CommandList: React.FC<CommandBaseProps>;
  export const CommandEmpty: React.FC<CommandBaseProps>;
  export const CommandGroup: React.FC<CommandBaseProps & { heading?: string }>;
  export const CommandSeparator: React.FC<CommandBaseProps>;
  export const CommandItem: React.FC<CommandBaseProps & { value?: string }>;
  export const CommandShortcut: React.FC<React.HTMLAttributes<HTMLSpanElement>>;

  // Falls eine Komponente fehlt, kann sie über den Index-Zugriff abgefangen
  // werden. Dies verhindert "Property does not exist on type…"-Fehler ohne
  // die gesamte (sehr große) Typdefinition hier abzubilden.
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  export const __unknownCommandComponent: any;
  // kein default-Export vorhanden → keine 'export default ...'-Anweisung
}