using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.IO;
using DiffMatchPatch;

namespace tdiff
{
    class Program
    {
        const char SEPARATEUR_TEXTES = (char)30; //Record separator char

        static void Main(string[] args)
        {
            String text1, text2;
            StringBuilder res = new StringBuilder();

            if(args.Count() == 1)
            {
                try
                {
                    var f = File.Open(args[0], FileMode.Open);

                    using (StreamReader sr = new StreamReader(f))
                    {
                        char[] buf = new char[1000];

                        while (!sr.EndOfStream)
                        {
                            int charlen = sr.Read(buf, 0, 1000);
                            for (int i = 0; i < charlen; ++i)
                            {
                                res.Append(buf[i]);
                            }
                        }
                    }
                    f.Close();
                }
                catch (IOException ioe)
                {
                    Console.WriteLine("Impossible de lire le fichier. " + ioe.ToString() );
                    return;
                }

                string[] textes = res.ToString().Split(SEPARATEUR_TEXTES);
                if (textes.Length != 2)
                {
                    Console.WriteLine("Fichier invalide. Le fichier est vide ou ne contient pas de separateur.");
                    return;
                }

                text1 = textes[0];
                text2 = textes[1];
            }
            else if (args.Count() == 2)
            {
                text1 = args[0];
                text2 = args[1];
            }
            else
            {
                Console.WriteLine("Parametres invalides.");
                return;
            }

            diff_match_patch diff = new diff_match_patch();
            List<Diff> diffs = diff.diff_main(text1, text2);
            foreach (Diff d in diffs)
                Console.WriteLine(d.ToString());
        }
    }
}
