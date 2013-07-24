using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using DiffMatchPatch;

namespace tdiff
{
    class Program
    {
        static void Main(string[] args)
        {
            if (args.Count() != 2)
            {
                Console.WriteLine("Parametres invalides.");
                return;
            }

            diff_match_patch diff = new diff_match_patch();
            List<Diff> diffs = diff.diff_main(args[0], args[1]);
            foreach (Diff d in diffs)
                Console.Write(d.ToString() + " ");
        }
    }
}
